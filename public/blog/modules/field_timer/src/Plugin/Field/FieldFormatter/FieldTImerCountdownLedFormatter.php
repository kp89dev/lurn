<?php

namespace Drupal\field_timer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'field_timer_countdown' formatter.
 *
 * @FieldFormatter(
 *   id = "field_timer_countdown_led",
 *   label = @Translation("jQuery Countdown LED"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class FieldTimerCountdownLedFormatter extends FieldTimerCountdownFormatterBase {

  /**
   * {@inheritdoc}
   */
  const JS_KEY = 'jquery.countdown.led';

  /**
   * LED color themes.
   */
  const
    LED_THEME_BLUE = 'blue',
    LED_THEME_GREEN = 'green';

  /**
   * Available count of days to display in formatter.
   */
  const
    LED_DAY_DIGITS_ONE = 1,
    LED_DAY_DIGITS_TWO = 2,
    LED_DAY_DIGITS_THREE = 3,
    LED_DAY_DIGITS_FOUR = 4;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = [
        'countdown_theme' => static::LED_THEME_GREEN,
        'max_count_of_days' => static::LED_DAY_DIGITS_TWO,
        'display_days' => 1,
        'display_hours' => 1,
        'display_minutes' => 1,
        'display_seconds' => 1,
      ] + parent::defaultSettings();

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    $keys = $this->getItemKeys($items);

    foreach ($items as $delta => $item) {
      $layout = $this->getLayout();
      $elements[$delta] = [
        '#markup' => '<div class="field-timer-jquery-countdown-led '
          . $this->getSetting('countdown_theme') . '" data-field-timer-key="' . $keys[$delta]
          . '" data-timestamp="' . $this->getTimestamp($item) . '">' . $layout . '</div>',
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['countdown_theme'] = [
      '#type' => 'select',
      '#title' => $this->t('Theme'),
      '#options' => $this->themeOptions(),
      '#default_value' => $this->getSetting('countdown_theme'),
    ];

    $form['display_days'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display days'),
      '#default_value' => $this->getSetting('display_days'),
      '#attributes' => ['class' => ['field-timer-display-days']],
    ];

    $form['max_count_of_days'] = [
      '#type' => 'select',
      '#title' => $this->t('Max count of days'),
      '#options' => $this->dayOptions(),
      '#default_value' => $this->getSetting('max_count_of_days'),
      '#states' => [
        'invisible' => [
          'input.field-timer-display-days' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['display_hours'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display hours'),
      '#default_value' => $this->getSetting('display_hours'),
    ];

    $form['display_minutes'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display minutes'),
      '#default_value' => $this->getSetting('display_minutes'),
    ];

    $form['display_seconds'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display seconds'),
      '#default_value' => $this->getSetting('display_seconds'),
    ];

    return $form;
  }

  /**
   * @inheritdoc
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $theme = $this->getSetting('countdown_theme');
    $max_count_of_days = $this->getSetting('max_count_of_days');

    $summary[] = t('Theme: %theme', ['%theme' => $this->themeOptions()[$theme]]);
    $summary[] = t('Display days: %display_days', ['%display_days' => $this->getSetting('display_days') ? $this->t('Yes') : $this->t('No')]);
    if ($this->getSetting('display_days')) {
      $summary[] = t('Maximum count of days: %max_count_of_days', ['%max_count_of_days' => $this->dayOptions()[$max_count_of_days]]);
    }
    $summary[] = t('Display hours: %display_hours', ['%display_hours' => $this->getSetting('display_hours') ? $this->t('Yes') : $this->t('No')]);
    $summary[] = t('Display minutes: %display_minutes', ['%display_minutes' => $this->getSetting('display_minutes') ? $this->t('Yes') : $this->t('No')]);
    $summary[] = t('Display seconds: %display_seconds', ['%display_seconds' => $this->getSetting('display_seconds') ? $this->t('Yes') : $this->t('No')]);

    return $summary;
  }

  /**
   * Renders timer/countdown layout.
   *
   * @return string
   */
  protected function getLayout() {
    $layout = '<span class="jquery-countdown-led-display-wrapper">';
    if ($this->getSetting('display_days')) {
      for ($i = $this->getSetting('max_count_of_days'); $i > 0; $i--) {
        $layout .= '<span class="%t% image{d1' . substr('000', 0, $i - 1) . '}"></span>';
      }
      $layout .= '<span class="%t% imageDay"></span><span class="%t% imageSpace"></span>';
    }
    if ($this->getSetting('display_hours')) {
      $layout .= '<span class="%t% image{h10}"></span><span class="%t% image{h1}"></span>';
    }
    if ($this->getSetting('display_minutes')) {
      $layout .= '<span class="%t% imageSep"></span>'
        . '<span class="%t% image{m10}"></span><span class="%t% image{m1}"></span>';
    }
    if ($this->getSetting('display_seconds')) {
      $layout .= '<span class="%t% imageSep"></span>'
        . '<span class="%t% image{s10}"></span><span class="%t% image{s1}"></span>';
    }

    return str_replace('%t%', $this->getSetting('countdown_theme'), $layout) . '</span>';
  }

  /**
   * Gets theme options.
   *
   * @return array
   */
  protected function themeOptions() {
    return [
      static::LED_THEME_GREEN => $this->t('Green'),
      static::LED_THEME_BLUE => $this->t('Blue'),
    ];
  }

  /**
   * Gets max number of days options.
   *
   * @return array
   */
  protected function dayOptions() {
    return [
      static::LED_DAY_DIGITS_ONE => 9,
      static::LED_DAY_DIGITS_TWO => 99,
      static::LED_DAY_DIGITS_THREE => 999,
      static::LED_DAY_DIGITS_FOUR => 9999,
    ];
  }

}