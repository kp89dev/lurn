<?php

/**
 * @file
 * Contains Drupal\antibot\Form\AntibotSettings.
 */

namespace Drupal\antibot\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AntibotSettings.
 *
 * @package Drupal\antibot\Form
 */
class AntibotSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'antibot.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'antibot_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('antibot.settings');
    $form['message'] = array(
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => t('Antibot requires that a user has Javascript enabled in order to use and submit a given form.'),
    );
    $form['form_ids'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Form IDs'),
      '#default_value' => implode("\r\n", $config->get('form_ids')),
      '#description' => $this->t('Specify the form IDs that should be protected by Antibot. Each form ID should be on a separate line. Wildcard (*) characters can be used.'),
    );
    $form['show_form_ids'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Display form IDs'),
      '#default_value' => $config->get('show_form_ids'),
      '#description' => $this->t('When enabled, the form IDs of all forms on every page will be displayed to any user with permission to access these settings. Also displayed will be whether or not Antibot is enabled for each form. This should only be turned on temporarily in order to easily determine the form IDs to use.'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('antibot.settings')
      ->set('form_ids', explode("\r\n", $form_state->getValue('form_ids')))
      ->set('show_form_ids', $form_state->getValue('show_form_ids'))
      ->save();
  }

}
