<?php

namespace Drupal\facebook_comments\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'facebook_comments' formatter.
 *
 * @FieldFormatter(
 *   id = "facebook_comments_formatter",
 *   label = @Translation("Facebook comments"),
 *   field_types = {
 *     "facebook_comments"
 *   }
 * )
 */
class FacebookCommentsFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'facebook_comments_style' => 'light',
      'facebook_comments_width' => 620,
      'facebook_comments_width_fluid' => 1,
      'facebook_comments_amount' => 15,
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = array();
    $element['facebook_comments_style'] = array(
      '#type' => 'select',
      '#title' => $this->t('Color Scheme'),
      '#default_value' => $this->getSetting('facebook_comments_style'),
      '#options' => array('light' => $this->t('Light'), 'dark' => $this->t('Dark')),
    );
    $element['facebook_comments_width'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Facebook comment plugin width'),
      '#default_value' => $this->getSetting('facebook_comments_width'),
      '#description' => $this->t('The width of the Facebook comment plugin in pixels. Example: 620'),
    );
    $element['facebook_comments_width_fluid'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Fluid Facebook comment plugin width'),
      '#default_value' => $this->getSetting('facebook_comments_width_fluid'),
      '#description' => $this->t('Make the width of the Facebook comment plugin fluid (100%).<br/>This overrules the width setting above.'),
    );
    $element['facebook_comments_amount'] = array(
      '#type' => 'select',
      '#title' => $this->t('Amount of comments to display'),
      '#options' => array(1 => 1, 2 => 2, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20, 30 => 30),
      '#default_value' => $this->getSetting('facebook_comments_amount'),
    );
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $style = $this->getSetting('facebook_comments_style');
    $width = $this->getSetting('facebook_comments_width');
    $fluid = $this->getSetting('facebook_comments_width_fluid');
    $amount = $this->getSetting('facebook_comments_amount');
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
      '#theme' => 'facebook_comments_field',
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
    $elements[] = $output;
    return $elements;
  }
}
