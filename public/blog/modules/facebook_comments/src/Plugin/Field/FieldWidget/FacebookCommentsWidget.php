<?php

namespace Drupal\facebook_comments\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'facebook_comments' field widget.
 *
 * @FieldWidget(
 *   id = "facebook_comments_widget",
 *   label = @Translation("Facebook comments"),
 *   field_types = {
 *     "facebook_comments"
 *   }
 * )
 */
class FacebookCommentsWidget extends WidgetBase {
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    return array();
  }
}
