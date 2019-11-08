<?php

namespace Drupal\poll\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\poll\PollInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays banned IP addresses.
 */
class PollViewForm extends FormBase {

  /**
   * The Poll of the form.
   *
   * @var \Drupal\poll\PollInterface
   */
  protected $poll;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'poll_view_form_' . $this->poll->id();
  }

  /**
   * Set the Poll of this form.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   The poll that will be set in the form.
   */
  public function setPoll(PollInterface $poll) {
    $this->poll = $poll;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL, $view_mode = 'full') {
    // Add the poll to the form.
    $form['poll']['#type'] = 'value';
    $form['poll']['#value'] = $this->poll;

    $form['#view_mode'] = $view_mode;

    if ($this->showResults($this->poll, $form_state)) {

      // Check if the user already voted. The form is still being built but
      // the Vote button won't be added so the submit callbacks will not be
      // called. Directly check for the request method and use the raw user
      // input.
      if ($request->isMethod('POST') && $this->poll->hasUserVoted()) {
        $input = $form_state->getUserInput();
        if (isset($input['op']) && $input['op'] == $this->t('Vote')) {
          // If this happened, then the form submission was likely a cached page.
          // Force a session for this user so he can see the results.
          drupal_set_message($this->t('Your vote for this poll has already been submitted.'), 'error');
          $_SESSION['poll_vote'][$this->poll->id()] = FALSE;
        }
      }

      $form['results'] = $this->showPollResults($this->poll);

      // For all view modes except full and block (as block displays it as the
      // block title), display the question.
      if ($view_mode != 'full' && $view_mode != 'block') {
        $form['results']['#show_question'] = TRUE;
      }
    }
    else {
      $options = $this->poll->getOptions();
      if ($options) {
        $form['choice'] = array(
          '#type' => 'radios',
          '#title' => t('Choices'),
          '#title_display' => 'invisible',
          '#options' => $options,
        );
      }
      $form['#theme'] = 'poll_vote';
      $form['#entity'] = $this->poll;
      $form['#action'] = $this->poll->url('canonical', ['query' => \Drupal::destination()->getAsArray()]);
      // Set a flag to hide results which will be removed if we want to view
      // results when the form is rebuilt.
      $form_state->set('show_results', FALSE);

      // For all view modes except full and block (as block displays it as the
      // block title), display the question.
      if ($view_mode != 'full' && $view_mode != 'block') {
        $form['#show_question'] = TRUE;
      }

    }

    $form['actions'] = $this->actions($form, $form_state, $this->poll);

    $form['#cache'] = array(
      'tags' => $this->poll->getCacheTags(),
    );

    return $form;
  }

  /**
   * Ajax callback to replace the poll form.
   */
  public function ajaxReplaceForm(array $form, FormStateInterface $form_state) {
    // Embed status message into the form.
    $form = ['messages' => ['#type' => 'status_messages']] + $form;
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    // Render the form.
    $output = $renderer->renderRoot($form);

    $response = new AjaxResponse();
    $response->setAttachments($form['#attached']);

    // Replace the form completely and return it.
    return $response->addCommand(new ReplaceCommand('.poll-view-form-' . $this->poll->id(), $output));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  public function showResults(PollInterface $poll, FormStateInterface $form_state) {
    $account = $this->currentUser();
    switch (TRUE) {
      // The "View results" button, when available, has been clicked.
      case $form_state->get('show_results'):
        return TRUE;

      // The poll is closed.
      case ($poll->isClosed()):
        return TRUE;

      // Anonymous user is trying to view a poll they aren't allowed to vote in.
      case ($account->isAnonymous() && !$poll->getAnonymousVoteAllow()):
        return TRUE;

      // The user has already voted.
      case ($poll->hasUserVoted()):
        return TRUE;

      default:
        return FALSE;
    }
  }

  protected function actions(array $form, FormStateInterface $form_state, $poll) {
    $actions = [];

    // Default ajax behavior, use the poll URL for faster submission, this
    // requires that we explicitly provide the ajax_form query argument too in
    // the separate options key, as that replaces all options of the Url object.
    $ajax = [
      'callback' => '::ajaxReplaceForm',
      'url' => $this->poll->toUrl(),
      'options' => ['query' => [FormBuilderInterface::AJAX_FORM_REQUEST => TRUE, 'view_mode' => $form['#view_mode']]]
    ];

    if ($this->showResults($poll, $form_state)) {
      // Allow user to cancel their vote.
      if ($this->isCancelAllowed($poll)) {
        $actions['#type'] = 'actions';
        $actions['cancel']['#type'] = 'submit';
        $actions['cancel']['#button_type'] = 'primary';
        $actions['cancel']['#value'] = t('Cancel vote');
        $actions['cancel']['#submit'] = array('::cancel');
        $actions['cancel']['#ajax'] = $ajax;
        $actions['cancel']['#weight'] = '0';
      }
      if (!$poll->hasUserVoted() && $poll->isOpen()) {
        $actions['#type'] = 'actions';
        $actions['back']['#type'] = 'submit';
        $actions['back']['#button_type'] = 'primary';
        $actions['back']['#value'] = t('View poll');
        $actions['back']['#submit'] = array('::back');
        $actions['back']['#ajax'] = $ajax;
        $actions['back']['#weight'] = '0';
      }
    }
    else {
      $actions['#type'] = 'actions';
      $actions['vote']['#type'] = 'submit';
      $actions['vote']['#button_type'] = 'primary';
      $actions['vote']['#value'] = t('Vote');
      $actions['vote']['#validate'] = array('::validateVote');
      $actions['vote']['#submit'] = array('::save');
      $actions['vote']['#ajax'] = $ajax;
      $actions['vote']['#weight'] = '0';

      // View results before voting.
      if ($poll->result_vote_allow->value) {
        $actions['result']['#type'] = 'submit';
        $actions['result']['#button_type'] = 'primary';
        $actions['result']['#value'] = t('View results');
        $actions['result']['#submit'] = array('::result');
        $actions['result']['#ajax'] = $ajax;
        $actions['result']['#weight'] = '1';
      }
    }

    return $actions;
  }

  /**
   * Display a themed poll results.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   The poll entity.
   * @param bool $block
   *   (optional) TRUE if a poll should be displayed in a block. Defaults to
   *   FALSE.
   *
   * @return array $output
   */
  function showPollResults(PollInterface $poll, $block = FALSE) {

    // Ensure that a page that shows poll results can not be cached.
    \Drupal::service('page_cache_kill_switch')->trigger();

    $total_votes = 0;
    foreach ($poll->getVotes() as $vote) {
      $total_votes += $vote;
    }

    $options = $poll->getOptions();
    $poll_results = array();
    foreach ($poll->getVotes() as $pid => $vote) {
      $percentage = round($vote * 100 / max($total_votes, 1));
      $display_votes = (!$block) ? ' (' . \Drupal::translation()
          ->formatPlural($vote, '1 vote', '@count votes') . ')' : '';

      $poll_results[] = array(
        '#theme' => 'poll_meter',
        '#choice' => $options[$pid],
        '#display_value' => t('@percentage%', array('@percentage' => $percentage)) . $display_votes,
        '#min' => 0,
        '#max' => $total_votes,
        '#value' => $vote,
        '#percentage' => $percentage,
        '#attributes' => array('class' => array('bar')),
      );
    }

    $output = array(
      '#theme' => 'poll_results',
      '#raw_question' => $poll->label(),
      '#results' => $poll_results,
      '#votes' => $total_votes,
      '#block' => $block,
      '#pid' => $poll->id(),
      '#vote' => isset($poll->vote) ? $poll->vote : NULL,
    );

    return $output;
  }



  /**
   * Cancel vote submit function.
   *
   * @param array $form
   *   The previous form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function cancel(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\poll\PollVoteStorageInterface $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    $vote_storage->cancelVote($this->poll, $this->currentUser());
    \Drupal::logger('poll')->notice('%user\'s vote in Poll #%poll deleted.', array(
      '%user' => $this->currentUser()->id(),
      '%poll' => $this->poll->id(),
    ));
    drupal_set_message($this->t('Your vote was cancelled.'));

    // In case of an ajax submission, trigger a form rebuild so that we can
    // return an updated form through the ajax callback.
    if ($this->getRequest()->query->get('ajax_form')) {
      $form_state->setRebuild(TRUE);
    }
  }

  /**
   * View vote results submit function.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function result(array $form, FormStateInterface $form_state) {
    $form_state->set('show_results', TRUE);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Back to poll view submit function.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function back(array $form, FormStateInterface $form_state) {
    $form_state->set('show_results', FALSE);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Save a user's vote submit function.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function save(array $form, FormStateInterface $form_state) {
    $options = array();
    $options['chid'] = $form_state->getValue('choice');
    $options['uid'] = $this->currentUser()->id();
    $options['pid'] = $form_state->getValue('poll')->id();
    $options['hostname'] = \Drupal::request()->getClientIp();
    $options['timestamp'] = REQUEST_TIME;
    // Save vote.
    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    $vote_storage->saveVote($options);
    drupal_set_message($this->t('Your vote has been recorded.'));

    if ($this->currentUser()->isAnonymous()) {
      // The vote is recorded so the user gets the result view instead of the
      // voting form when viewing the poll. Saving a value in $_SESSION has the
      // convenient side effect of preventing the user from hitting the page
      // cache. When anonymous voting is allowed, the page cache should only
      // contain the voting form, not the results.
      $_SESSION['poll_vote'][$form_state->getValue('poll')->id()] = $form_state->getValue('choice');
    }

    // In case of an ajax submission, trigger a form rebuild so that we can
    // return an updated form through the ajax callback.
    if ($this->getRequest()->query->get('ajax_form')) {
      $form_state->setRebuild(TRUE);
    }

    // No explicit redirect, so that we stay on the current page, which might
    // be the poll form or another page that is displaying this poll, for
    // example as a block.
  }

  /**
   * Validates the vote action.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function validateVote(array &$form, FormStateInterface $form_state) {
    if (!$form_state->hasValue('choice')) {
      $form_state->setErrorByName('choice', $this->t('Your vote could not be recorded because you did not select any of the choices.'));
    }
  }

  /**
   * Checks if the current user is allowed to cancel on the given poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *
   * @return bool
   *   TRUE if the user can cancel.
   */
  protected function isCancelAllowed(PollInterface $poll) {
    // Allow access if the user has voted.
    return $poll->hasUserVoted()
      // And the poll allows to cancel votes.
      && $poll->getCancelVoteAllow()
      // And the user has the cancel own vote permission.
      && $this->currentUser()->hasPermission('cancel own vote')
      // And the user is authenticated or his session contains the voted flag.
      && (\Drupal::currentUser()->isAuthenticated() || !empty($_SESSION['poll_vote'][$poll->id()]));
  }

}
