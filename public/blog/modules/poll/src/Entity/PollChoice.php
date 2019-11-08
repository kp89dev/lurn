<?php

namespace Drupal\poll\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\poll\PollChoiceInterface;

/**
 * Defines the poll choice entity class.
 *
 * @ContentEntityType(
 *   id = "poll_choice",
 *   label = @Translation("Poll Choice"),
 *   base_table = "poll_choice",
 *   data_table = "poll_choice_field_data",
 *   admin_permission = "administer polls",
 *   content_translation_ui_skip = TRUE,
 *   translatable = TRUE,
 *   content_translation_metadata = "Drupal\poll\PollChoiceTranslationMetadataWrapper",
 *   handlers = {
 *     "translation" = "Drupal\poll\PollChoiceTranslationHandler",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "choice",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode"
 *   }
 * )
 */
class PollChoice extends ContentEntityBase implements PollChoiceInterface {

  /**
   * @var bool
   */
  protected $needsSave = NULL;

  /**
   * {@inheritdoc}
   */
  public function setChoice($question) {
    $this->set('choice', $question);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function needsSaving($new_value = NULL) {
    // If explicitly set, return that value. otherwise fall back to isNew(),
    // saving is always required for new entities.
    $return = $this->needsSave !== NULL ? $this->needsSave : $this->isNew();

    if ($new_value !== NULL) {
      $this->needsSave = $new_value;
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Choice ID'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setReadOnly(TRUE);

    $fields['choice'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Choice'))
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

    return $fields;
  }

}
