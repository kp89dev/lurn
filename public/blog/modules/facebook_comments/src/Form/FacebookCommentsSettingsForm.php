<?php
/**
 * @file
 * Contains \Drupal\facebook_comments\Form\FacebookCommentsSettingsForm
 */

namespace Drupal\facebook_comments\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

class FacebookCommentsSettingsForm extends ConfigFormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'facebook_comments_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'facebook_comments.settings'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $config = \Drupal::config('facebook_comments.settings');
    $form['facebook_comments_appid'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Facebook App ID'),
      '#default_value' => $config->get('facebook_comments_appid'),
      '#description' => $this->t('Enter the Facebook App ID to ensure that all comments can be grouped for moderation.'),
    );
    $form['facebook_comments_admins'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Facebook Admins'),
      '#default_value' => $config->get('facebook_comments_admins'),
      '#description' => $this->t('Enter a comma-seperated list of all Facebook admin user id\'s to ensure that all comments can be grouped for moderation.<br/>If you enter an App ID, the Admins will be ignored. For more information read the <a href=":url">developer documentation</a>.', array(':url' => 'https://developers.facebook.com/docs/plugins/comments#moderation-setup-instructions')),
    );
    $form['facebook_comments_ssl'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('SSL support'),
      '#default_value' => $config->get('facebook_comments_ssl'),
      '#description' => $this->t('Enable support for SSL. Warning: you might lose comments on existing pages.'),
    );
    return parent::buildForm($form,$form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('facebook_comments.settings')
      ->set('facebook_comments_appid', $form_state->getValue('facebook_comments_appid'))
      ->set('facebook_comments_admins', $form_state->getValue('facebook_comments_admins'))
      ->set('facebook_comments_ssl', $form_state->getValue('facebook_comments_ssl'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
