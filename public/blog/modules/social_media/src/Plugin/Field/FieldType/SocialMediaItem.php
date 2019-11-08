<?php

namespace Drupal\social_media\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'social_media' field type.
 *
 * @FieldType(
 *   id = "social_media",
 *   label = @Translation("Social media"),
 *   module = "social_media",
 *   description = @Translation("Social media as field plugin"),
 *   default_widget = "social_media_default",
 *   default_formatter = "social_media_default"
 * )
 */
class SocialMediaItem extends FieldItemBase implements OptionsProviderInterface  {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'int',
          'size' => 'tiny',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'on_label' => new TranslatableMarkup('On'),
      'off_label' => new TranslatableMarkup('Off'),
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('boolean')
      ->setLabel(t('Social media value'))
      ->setRequired(TRUE);

    return $properties;
  }


  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $element['on_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('"On" label'),
      '#default_value' => $this->getSetting('on_label'),
      '#required' => TRUE,
    ];
    $element['off_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('"Off" label'),
      '#default_value' => $this->getSetting('off_label'),
      '#required' => TRUE,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    return [0, 1];
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      0 => $this->getSetting('off_label'),
      1 => $this->getSetting('on_label'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    return [0, 1];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    return $this->getPossibleOptions($account);
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['value'] = mt_rand(0, 1);
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

}
