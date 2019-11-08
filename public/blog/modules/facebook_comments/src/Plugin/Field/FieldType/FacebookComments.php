<?php

namespace Drupal\facebook_comments\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'facebook_comments' field type.
 *
 * @FieldType(
 *   id = "facebook_comments",
 *   label = @Translation("Facebook comments"),
 *   default_formatter = "facebook_comments_formatter",
 *   default_widget = "facebook_comments_widget"
 * )
 */
class FacebookComments extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string');
    return $properties;
  }
}
