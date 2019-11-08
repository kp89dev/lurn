<?php

namespace Drupal\poll\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the poll edit forms.
 */
class PollForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $poll = $this->entity;

    $form['#title'] = $this->t('Edit @label', ['@label' => $poll->label()]);

    foreach ($form['choice']['widget'] as $key => $choice) {
      if (is_int($key) && $form['choice']['widget'][$key]['choice']['#default_value'] != NULL) {
        $form['choice']['widget'][$key]['choice']['#attributes'] = ['class' => ['poll-existing-choice']];
      }
    }

    $form['#attached'] = ['library' => ['poll/admin']];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $poll = $this->buildEntity($form, $form_state);
    // Check for duplicate titles.
    $poll_storage = $this->entityManager->getStorage('poll');
    $result = $poll_storage->getPollDuplicates($poll);
    foreach ($result as $item) {
      if (strcasecmp($item->label(), $poll->label()) == 0) {
        $form_state->setErrorByName('question', $this->t('A feed named %feed already exists. Enter a unique question.', array('%feed' => $poll->label())));
      }
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $poll = $this->entity;
    $insert = (bool) $poll->id();
    $poll->save();
    if ($insert) {
      drupal_set_message($this->t('The poll %poll has been updated.', array('%poll' => $poll->label())));
    }
    else {
      \Drupal::logger('poll')->notice('Poll %poll added.', array('%poll' => $poll->label(), 'link' => $poll->link($poll->label())));
      drupal_set_message($this->t('The poll %poll has been added.', array('%poll' => $poll->label())));
    }

    $form_state->setRedirect('poll.poll_list');
  }

}
