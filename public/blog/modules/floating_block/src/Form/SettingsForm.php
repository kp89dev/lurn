<?php

/**
 * @file
 * Contains \Drupal\floating_block\Form\SettingsForm.
 */

namespace Drupal\floating_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures floating_block settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'floating_block_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $blocks = $this->configFactory->get('floating_block.settings')->get('blocks');
    $form['blocks'] = array(
      '#type' => 'textarea',
      '#title' => t('Floating block settings'),
      '#default_value' => _floating_block_admin_convert_array_to_text($blocks),
      '#description' => t('Floating block configurations, one per line in the formation <code>[css selector]<strong>|</strong>[extra settings]</code>.'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $array = _floating_block_admin_convert_text_to_array($values['blocks']);
    $string = _floating_block_admin_convert_array_to_text($array);

    // Compare that floating block settings string to array conversion is
    // idempotent. New line characters \n and \r get make comparison difficult.
    if (str_replace(array("\n", "\r"), '', $string) != str_replace(array("\n", "\r"), '', $values['blocks'])) {
      $form_state->setErrorByName('blocks', $this->t('Each line must of the format: <code>selector|setting_key=setting_value,setting_key=setting_value,...</code>'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configFactory->getEditable('floating_block.settings')
      ->set('blocks', _floating_block_admin_convert_text_to_array($values['blocks']))
      ->save();
  }

}
