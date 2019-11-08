<?php

namespace Drupal\facebook_comments\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a Facebook Comments Block
 *
 * @Block(
 *   id = "facebook_comments",
 *   admin_label = @Translation("Facebook comments"),
 * )
 */
class FacebookCommentsBlock extends BlockBase implements BlockPluginInterface {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $style = $config['facebook_comments_style'];
    $width = $config['facebook_comments_width'];
    $fluid = $config['facebook_comments_width_fluid'];
    $amount = $config['facebook_comments_amount'];
    $config = \Drupal::config('facebook_comments.settings');
    $appid = $config->get('facebook_comments_appid');
    $admins = $config->get('facebook_comments_admins');
    $ssl = $config->get('facebook_comments_ssl');
    $options = array('absolute' => TRUE);
    $url = Url::fromRoute('<current>', array(), $options)->toString();
    $lang = _facebook_comments_get_language_code();
    // If the path is non-SSL, rewrite it to SSL.
    if ($ssl && strpos($url, "http://") !== FALSE) {
      $url = str_ireplace("http://", "https://", $url);
    }
    if ($fluid) {
      $class = "fb-comments-fluid";
    }
    else {
      $class = "";
    }
    $output = array(
      '#theme' => 'facebook_comments_block',
      '#style' => $style,
      '#amount' => $amount,
      '#width' => $width,
      '#class' => $class,
      '#url' => $url,
      '#lang' => $lang,
    );
    // Display Facebook comments with fluid width
    if ($fluid) {
      $output['#attached']['library'][] = 'facebook_comments/fluid';
    }
    // Add the Facebook App ID if it exists
    if ($appid) {
      $a = array(
        '#tag' => 'meta',
        '#attributes' => array(
          'name' => 'fb:app_id',
          'content' => $appid,
        ),
      );
      $output['#attached']['html_head'][] = [$a, 'facebook_comments'];
    }
    // Fallback to Facebook Admins if they exists
    elseif ($admins) {
      $admin = explode(",", $admins);
      foreach($admin as $key => $value) {
        $a = array(
          '#tag' => 'meta',
          '#attributes' => array(
            'name' => 'fb:admins',
            'content' => trim($value),
          ),
        );
        $output['#attached']['html_head'][] = [$a, 'facebook_comments_'. $key];
      }
    }
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['facebook_comments_style'] = array(
      '#type' => 'select',
      '#title' => $this->t('Color Scheme'),
      '#default_value' => isset($config['facebook_comments_style']) ? $config['facebook_comments_style'] : 'light',
      '#options' => array('light' => $this->t('Light'), 'dark' => $this->t('Dark')),
    );
    $form['facebook_comments_width'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Facebook comment plugin width'),
      '#default_value' => isset($config['facebook_comments_width']) ? $config['facebook_comments_width'] : 208,
      '#description' => $this->t('The width of the Facebook comment plugin for this block, in pixels. Example: 208'),
    );
    $form['facebook_comments_width_fluid'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Fluid Facebook comment plugin width'),
      '#default_value' => isset($config['facebook_comments_width_fluid']) ? $config['facebook_comments_width_fluid'] : 1,
      '#description' => $this->t('Make the width of the Facebook comment plugin for this block fluid (100%). This overrules the block width setting above.'),
    );
    $form['facebook_comments_amount'] = array(
      '#type' => 'select',
      '#title' => $this->t('Amount of comments to display'),
      '#options' => array(1 => 1, 2 => 2, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
      '#default_value' => isset($config['facebook_comments_amount']) ? $config['facebook_comments_amount'] : 5,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('facebook_comments_style', $form_state->getValue('facebook_comments_style'));
    $this->setConfigurationValue('facebook_comments_width', $form_state->getValue('facebook_comments_width'));
    $this->setConfigurationValue('facebook_comments_width_fluid', $form_state->getValue('facebook_comments_width_fluid'));
    $this->setConfigurationValue('facebook_comments_amount', $form_state->getValue('facebook_comments_amount'));
  }

}
