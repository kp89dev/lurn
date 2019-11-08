<?php

namespace Drupal\poll\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\poll\PollInterface;
use Drupal\user\UserInterface;

/**
 * Defines the poll entity class.
 *
 * @ContentEntityType(
 *   id = "poll",
 *   label = @Translation("Poll"),
 *   handlers = {
 *     "access" = "\Drupal\poll\PollAccessControlHandler",
 *     "storage" = "Drupal\poll\PollStorage",
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler",
 *     "list_builder" = "Drupal\poll\PollListBuilder",
 *     "view_builder" = "Drupal\poll\PollViewBuilder",
 *     "views_data" = "Drupal\poll\PollViewData",
 *     "form" = {
 *       "default" = "Drupal\poll\Form\PollForm",
 *       "edit" = "Drupal\poll\Form\PollForm",
 *       "delete" = "Drupal\poll\Form\PollDeleteForm",
 *       "delete_vote" = "Drupal\poll\Form\PollVoteDeleteForm",
 *       "delete_items" = "Drupal\poll\Form\PollItemsDeleteForm",
 *     }
 *   },
 *   links = {
 *     "canonical" = "/poll/{poll}",
 *     "edit-form" = "/poll/{poll}/edit",
 *     "delete-form" = "/poll/{poll}/delete"
 *   },
 *   base_table = "poll",
 *   data_table = "poll_field_data",
 *   admin_permission = "administer polls",
 *   field_ui_base_route = "poll.settings",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "question",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode"
 *   }
 * )
 */
class Poll extends ContentEntityBase implements PollInterface {

  /**
   * {@inheritdoc}
   */
  public function setQuestion($question) {
    $this->set('question', $question);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreated($created) {
    $this->set('created', $created);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreated() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntime() {
    return $this->get('runtime')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRuntime($runtime) {
    $this->set('runtime', $runtime);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAnonymousVoteAllow() {
    return $this->get('anonymous_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setAnonymousVoteAllow($anonymous_vote_allow) {
    $this->set('anonymous_vote_allow', $anonymous_vote_allow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelVoteAllow() {
    return $this->get('cancel_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCancelVoteAllow($cancel_vote_allow) {
    $this->set('cancel_vote_allow', $cancel_vote_allow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getResultVoteAllow() {
    return $this->get('result_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setResultVoteAllow($result_vote_allow) {
    $this->set('result_vote_allow', $result_vote_allow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isOpen() {
    return (bool) $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isClosed() {
    return (bool) $this->get('status')->value == 0;
  }

  /**
   * {@inheritdoc}
   */
  public function close() {
    return $this->set('status', 0);
  }

  /**
   * {@inheritdoc}
   */
  public function open() {
    return $this->set('status', 1);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Poll ID'))
      ->setDescription(t('The ID of the poll.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setDescription(t('The poll author.'))
      ->setSetting('target_type', 'user')
      ->setTranslatable(TRUE)
      ->setDefaultValueCallback('Drupal\poll\Entity\Poll::getCurrentUserId')
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => -10,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The poll UUID.'))
      ->setReadOnly(TRUE);

    $fields['question'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Question'))
      ->setDescription(t('The poll question.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -100,
      ));

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The poll language code.'));

    $fields['choice'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Choice'))
      ->setSetting('target_type', 'poll_choice')
      ->setDescription(t('Enter the poll choices.'))
      ->setRequired(TRUE)
      // The number and order of choices may not be translated, only the
      // referenced choices.
      ->setTranslatable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'poll_choice_default',
        'settings' => [],
        'weight' => -20,
      ]);

    // Poll attributes
    $duration = array(
      // 1-6 days.
      86400,
      2 * 86400,
      3 * 86400,
      4 * 86400,
      5 * 86400,
      6 * 86400,
      // 1-3 weeks (7 days).
      604800,
      2 * 604800,
      3 * 604800,
      // 1-3,6,9 months (30 days).
      2592000,
      2 * 2592000,
      3 * 2592000,
      6 * 2592000,
      9 * 2592000,
      // 1 year (365 days).
      31536000,
    );

    $period = array(0 => t('Unlimited')) + array_map(array(\Drupal::service('date.formatter'), 'formatInterval'), array_combine($duration, $duration));

    $fields['runtime'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Poll Duration'))
      ->setDescription(t('After this period, the poll will be closed automatically.'))
      ->setSetting('unsigned', TRUE)
      ->setRequired(TRUE)
      ->setSetting('allowed_values', $period)
      ->setDefaultValue(0)
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 0,
      ));

    $fields['anonymous_vote_allow'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Allow anonymous votes'))
      ->setDescription(t('A flag indicating whether anonymous users are allowed to vote.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'settings' => array(
          'display_label' => TRUE,
        ),
        'weight' => 1,
      ));

    $fields['cancel_vote_allow'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Allow cancel votes'))
      ->setDescription(t('A flag indicating whether users may cancel their vote.'))
      ->setDefaultValue(1)
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'settings' => array(
          'display_label' => TRUE,
        ),
        'weight' => 2,
      ));

    $fields['result_vote_allow'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Allow view results'))
      ->setDescription(t('A flag indicating whether users may see the results before voting.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'settings' => array(
          'display_label' => TRUE,
        ),
        'weight' => 3,
      ));

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Active'))
      ->setDescription(t('A flag indicating whether the poll is active.'))
      ->setDefaultValue(1)
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'settings' => array(
          'display_label' => TRUE,
        ),
        'weight' => -5,
      ));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('When the poll was created, as a Unix timestamp.'));

    return $fields;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return array(\Drupal::currentUser()->id());
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function sort($a, $b) {
    return strcmp($a->label(), $b->label());
  }


  /**
   * @todo: Refactor - doesn't belong here.
   *
   * @return mixed
   */
  public function hasUserVoted() {
    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    return $vote_storage->getUserVote($this);
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    $options = array();
    if (count($this->choice)) {
      foreach ($this->choice as $choice_item) {
        $options[$choice_item->target_id] = \Drupal::service('entity.repository')->getTranslationFromContext($choice_item->entity, $this->language()->getId())->label();
      }
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptionValues() {
    $options = array();
    if (count($this->choice)) {
      foreach ($this->choice as $choice_item) {
        $options[$choice_item->target_id] = 1;
      }
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    foreach ($this->choice as $choice_item) {
      if ($choice_item->entity && $choice_item->entity->needsSaving()) {
        $choice_item->entity->save();
        $choice_item->target_id = $choice_item->entity->id();
      }
    }

    // Delete no longer referenced choices.
    if (!$this->isNew()) {
      $original_choices = [];
      foreach ($this->original->choice as $choice_item) {
        $original_choices[] = $choice_item->target_id;
      }

      $current_choices = [];
      foreach ($this->choice as $choice_item) {
        $current_choices[] = $choice_item->target_id;
      }

      $removed_choices = array_diff($original_choices, $current_choices);
      if ($removed_choices) {
        \Drupal::service('poll_vote.storage')->deleteChoicesVotes($removed_choices);
        $storage = \Drupal::entityTypeManager()->getStorage('poll_choice');
        $storage->delete($storage->loadMultiple($removed_choices));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Delete votes.
    foreach ($entities as $entity) {
      $storage->deleteVotes($entity);
    }

    // Delete referenced choices.
    $choices = [];
    foreach ($entities as $entity) {
      $choices = array_merge($choices, $entity->choice->referencedEntities());
    }
    if ($choices) {
      \Drupal::entityTypeManager()->getStorage('poll_choice')->delete($choices);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getVotes() {
    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    return $vote_storage->getVotes($this);
  }

}
