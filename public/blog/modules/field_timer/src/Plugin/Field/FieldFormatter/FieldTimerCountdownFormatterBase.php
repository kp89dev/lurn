<?php

namespace Drupal\field_timer\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Url;


/**
 * Base implementation of formatters that uses jQuery Countdown plugin.
 */
abstract class FieldTimerCountdownFormatterBase extends FieldTimerJsFormatterBase {

  /**
   * {@inheritdoc}
   */
  const LIBRARY_NAME = 'jquery.countdown';

  /**
   * Formatter types.
   */
  const
    TYPE_AUTO = 'auto',
    TYPE_TIMER = 'timer',
    TYPE_COUNTDOWN = 'countdown';

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = [
      'type' => static::TYPE_AUTO,
    ] + parent::defaultSettings();

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => $this->typeOptions(),
      '#default_value' => $this->getSetting('type'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $type = $this->getSetting('type');
    $summary[] = $this->t('Type: @type', ['@type' => $this->typeOptions()[$type]]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  protected function preparePluginSettings(FieldItemInterface $item) {
    $settings = $this->getSettings();
    $timestamp = $this->getTimestamp($item);
    $type = $this->getSetting('type');

    if ($type == 'timer' || ($type == 'auto' && $timestamp <= REQUEST_TIME)) {
      $settings['until'] = FALSE;
      $settings['since'] = TRUE;
    }
    elseif ($type == 'countdown' || ($type == 'auto' && $timestamp > REQUEST_TIME)) {
      $settings['until'] = TRUE;
      $settings['since'] = FALSE;
    }

    unset($settings['type']);
    return $settings;
  }

  protected function typeOptions() {
    return [
      static::TYPE_AUTO => $this->t('Auto'),
      static::TYPE_TIMER => $this->t('Timer'),
      static::TYPE_COUNTDOWN => $this->t('Countdown'),
    ];
  }

  protected function getDocumentationLink(array $options = []) {
    return Url::fromUri('http://keith-wood.name/countdownRef.html', $options)->toString();
  }

}