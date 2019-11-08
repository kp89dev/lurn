<?php
/**
 * @file
 * Contains \Drupal\fb_likebox\Plugin\Block\FBLikeboxBlock.
 */

namespace Drupal\fb_likebox\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a configurable block with Facebook Likebox's plugin.
 *
 * @Block(
 *  id = "fb_likebox_block",
 *  admin_label = @Translation("FB Likebox"),
 *  category = @Translation("FB Likebox"),
 * )
 */
class FBLikeboxBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $config = $this->getConfiguration();

    // Facebook Widget settings.
    $form['fb_likebox_display_settings'] = array(
      '#type' => 'details',
      '#title' => $this->t('Display options'),
      '#open' => TRUE,
    );
    $form['fb_likebox_display_settings']['url'] = array(
      '#type' => 'url',
      '#title' => $this->t('Facebook Page URL'),
      '#default_value' => $config['url'],
      '#description' => $this->t('Enter the Facebook Page URL. I.e.: https://www.facebook.com/FacebookDevelopers'),
      '#required' => TRUE,
    );
    $form['fb_likebox_display_settings']['app_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Facebook App ID'),
      '#default_value' => $config['app_id'],
    );
    $form['fb_likebox_display_settings']['hide_header'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Hide cover photo in the header'),
      '#default_value' => $config['hide_header'],
    );
    $form['fb_likebox_display_settings']['stream'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t("Show posts from the Page's timeline"),
      '#default_value' => $config['stream'],
    );
    $form['fb_likebox_display_settings']['show_faces'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Show profile photos when friends like this'),
      '#default_value' => $config['show_faces'],
    );
    $form['fb_likebox_display_settings']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('iFrame title attribute'),
      '#default_value' => $config['title'],
      '#description' => $this->t('The value of the title attribute.'),
      '#required' => TRUE,
    );
    $form['fb_likebox_display_settings']['width'] = array(
      '#type' => 'number',
      '#title' => $this->t('Width'),
      '#default_value' => $config['width'],
      '#min' => 180,
      '#max' => 500,
      '#description' => $this->t('The width of the Facebook likebox. Must be a number between 180 and 500, limits included.'),
      '#required' => TRUE,
    );
    $form['fb_likebox_display_settings']['height'] = array(
      '#type' => 'number',
      '#title' => $this->t('Height'),
      '#default_value' => $config['height'],
      '#min' => 70,
      '#description' => $this->t('The height of the plugin in pixels. Must be a number bigger than 70.'),
      '#required' => TRUE,
    );
    $form['fb_likebox_display_settings']['hide_cta'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Hide the custom call to action button (if available)'),
      '#default_value' => $config['hide_cta'],
    );
    $form['fb_likebox_display_settings']['small_header'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Use the small header instead'),
      '#default_value' => $config['small_header'],
    );
    $form['fb_likebox_display_settings']['adapt_container_width'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Try to fit inside the container width'),
      '#default_value' => $config['adapt_container_width'],
    );
    $form['fb_likebox_display_settings']['language'] = array(
      '#type' => 'select',
      '#title' => t('Choose your language'),
      '#options' => $this->likeboxLanguages(),
      '#default_value' => $config['language'],
    );
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $display_settings = $form_state->getValue('fb_likebox_display_settings');
    foreach ($display_settings as $key => $value) {
      $this->setConfigurationValue($key, $value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    $render['root-div'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => ['fb-root'],
      ],
    ];

    $render['block'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['fb-page'],
        'data-href' => $config['url'],
        'data-width' => $config['width'],
        'data-height' => $config['height'],
        'data-hide-cover' => $config['hide_header'],
        'data-show-facepile' => $config['show_faces'],
        'data-show-posts' => $config['stream'],
        'data-hide-cta' => $config['hide_cta'],
        'data-small-header' => $config['small_header'],
        'data-adapt-container-width' => $config['adapt_container_width'],
      ],
    ];

    $render['block']['child'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['fb-xfbml-parse-ignore'],
      ],
    ];
    $render['block']['child']['blockquote'] = [
      '#type' => 'link',
      '#title' => $config['title'],
      '#href' => $config['url'],
      '#prefix' => '<blockquote cite="' . $config['url'] . '">',
      '#suffix' => '</blockquote>',
    ];
    $render['#attached']['library'][] = 'fb_likebox/drupal.fb_likebox';
    $render['#attached']['drupalSettings']['fbLikeboxAppId'] = $config['app_id'];
    $render['#attached']['drupalSettings']['fbLikeboxLanguage'] = $config['language'];
    return $render;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'url' => '',
      'app_id' => '',
      'hide_header' => '',
      'stream' => '',
      'show_faces' => '',
      'title' => '',
      'width' => '',
      'height' => '',
      'hide_cta' => '',
      'small_cta' => '',
      'small_header' => '',
      'adapt_container_width' => '',
      'language' => [],
    ];
  }

  /**
   * Returns a list of all available facebook likebox languages.
   *
   * @return array
   *   Returns a list of all available facebook likebox languages.
   */
  protected function likeboxLanguages() {
    return [
      'af_ZA' => $this->t('Afrikaans'),
      'ak_GH' => $this->t('Akan'),
      'am_ET' => $this->t('Amharic'),
      'ar_AR' => $this->t('Arabic'),
      'as_IN' => $this->t('Assamese'),
      'ay_BO' => $this->t('Aymara'),
      'az_AZ' => $this->t('Azerbaijani'),
      'be_BY' => $this->t('Belarusian'),
      'bg_BG' => $this->t('Bulgarian'),
      'bn_IN' => $this->t('Bengali'),
      'br_FR' => $this->t('Breton'),
      'bs_BA' => $this->t('Bosnian'),
      'ca_ES' => $this->t('Catalan'),
      'cb_IQ' => $this->t('Sorani Kurdish'),
      'ck_US' => $this->t('Cherokee'),
      'co_FR' => $this->t('Corsican'),
      'cs_CZ' => $this->t('Czech'),
      'cx_PH' => $this->t('Cebuano'),
      'cy_GB' => $this->t('Welsh'),
      'da_DK' => $this->t('Danish'),
      'de_DE' => $this->t('German'),
      'el_GR' => $this->t('Greek'),
      'en_EN' => $this->t('English'),
      'en_GB' => $this->t('English (UK)'),
      'en_IN' => $this->t('English (India)'),
      'en_PI' => $this->t('English (Pirate)'),
      'en_UD' => $this->t('English (Upside Down)'),
      'en_US' => $this->t('English (US)'),
      'eo_EO' => $this->t('Esperanto'),
      'es_CL' => $this->t('Spanish (Chile)'),
      'es_CO' => $this->t('Spanish (Colombia)'),
      'es_ES' => $this->t('Spanish (Spain)'),
      'es_LA' => $this->t('Spanish'),
      'es_MX' => $this->t('Spanish (Mexico)'),
      'es_VE' => $this->t('Spanish (Venezuela)'),
      'et_EE' => $this->t('Estonian'),
      'eu_ES' => $this->t('Basque'),
      'fa_IR' => $this->t('Persian'),
      'fb_LT' => $this->t('Leet Speak'),
      'ff_NG' => $this->t('Fulah'),
      'fi_FI' => $this->t('Finnish'),
      'fo_FO' => $this->t('Faroese'),
      'fr_CA' => $this->t('French (Canada)'),
      'fr_FR' => $this->t('French (France)'),
      'fy_NL' => $this->t('Frisian'),
      'ga_IE' => $this->t('Irish'),
      'gl_ES' => $this->t('Galician'),
      'gn_PY' => $this->t('Guarani'),
      'gu_IN' => $this->t('Gujarati'),
      'gx_GR' => $this->t('Classical Greek'),
      'ha_NG' => $this->t('Hausa'),
      'he_IL' => $this->t('Hebrew'),
      'hi_IN' => $this->t('Hindi'),
      'hr_HR' => $this->t('Croatian'),
      'hu_HU' => $this->t('Hungarian'),
      'hy_AM' => $this->t('Armenian'),
      'id_ID' => $this->t('Indonesian'),
      'ig_NG' => $this->t('Igbo'),
      'is_IS' => $this->t('Icelandic'),
      'it_IT' => $this->t('Italian'),
      'ja_JP' => $this->t('Japanese'),
      'ja_KS' => $this->t('Japanese (Kansai)'),
      'jv_ID' => $this->t('Javanese'),
      'ka_GE' => $this->t('Georgian'),
      'kk_KZ' => $this->t('Kazakh'),
      'km_KH' => $this->t('Khmer'),
      'kn_IN' => $this->t('Kannada'),
      'ko_KR' => $this->t('Korean'),
      'ku_TR' => $this->t('Kurdish (Kurmanji)'),
      'la_VA' => $this->t('Latin'),
      'lg_UG' => $this->t('Ganda'),
      'li_NL' => $this->t('Limburgish'),
      'ln_CD' => $this->t('Lingala'),
      'lo_LA' => $this->t('Lao'),
      'lt_LT' => $this->t('Lithuanian'),
      'lv_LV' => $this->t('Latvian'),
      'mg_MG' => $this->t('Malagasy'),
      'mk_MK' => $this->t('Macedonian'),
      'ml_IN' => $this->t('Malayalam'),
      'mn_MN' => $this->t('Mongolian'),
      'mr_IN' => $this->t('Marathi'),
      'ms_MY' => $this->t('Malay'),
      'mt_MT' => $this->t('Maltese'),
      'my_MM' => $this->t('Burmese'),
      'nb_NO' => $this->t('Norwegian (bokmal)'),
      'nd_ZW' => $this->t('Ndebele'),
      'ne_NP' => $this->t('Nepali'),
      'nl_BE' => $this->t('Dutch (België)'),
      'nl_NL' => $this->t('Dutch'),
      'nn_NO' => $this->t('Norwegian (nynorsk)'),
      'ny_MW' => $this->t('Chewa'),
      'or_IN' => $this->t('Oriya'),
      'pa_IN' => $this->t('Punjabi'),
      'pl_PL' => $this->t('Polish'),
      'ps_AF' => $this->t('Pashto'),
      'pt_BR' => $this->t('Portuguese (Brazil)'),
      'pt_PT' => $this->t('Portuguese (Portugal)'),
      'qu_PE' => $this->t('Quechua'),
      'rm_CH' => $this->t('Romansh'),
      'ro_RO' => $this->t('Romanian'),
      'ru_RU' => $this->t('Russian'),
      'rw_RW' => $this->t('Kinyarwanda'),
      'sa_IN' => $this->t('Sanskrit'),
      'sc_IT' => $this->t('Sardinian'),
      'se_NO' => $this->t('Northern Sámi'),
      'si_LK' => $this->t('Sinhala'),
      'sk_SK' => $this->t('Slovak'),
      'sl_SI' => $this->t('Slovenian'),
      'sn_ZW' => $this->t('Shona'),
      'so_SO' => $this->t('Somali'),
      'sq_AL' => $this->t('Albanian'),
      'sr_RS' => $this->t('Serbian'),
      'sv_SE' => $this->t('Swedish'),
      'sw_KE' => $this->t('Swahili'),
      'sy_SY' => $this->t('Syriac'),
      'sz_PL' => $this->t('Silesian'),
      'ta_IN' => $this->t('Tamil'),
      'te_IN' => $this->t('Telugu'),
      'tg_TJ' => $this->t('Tajik'),
      'th_TH' => $this->t('Thai'),
      'tk_TM' => $this->t('Turkmen'),
      'tl_PH' => $this->t('Filipino'),
      'tl_ST' => $this->t('Klingon'),
      'tr_TR' => $this->t('Turkish'),
      'tt_RU' => $this->t('Tatar'),
      'tz_MA' => $this->t('Tamazight'),
      'uk_UA' => $this->t('Ukrainian'),
      'ur_PK' => $this->t('Urdu'),
      'uz_UZ' => $this->t('Uzbek'),
      'vi_VN' => $this->t('Vietnamese'),
      'wo_SN' => $this->t('Wolof'),
      'xh_ZA' => $this->t('Xhosa'),
      'yi_DE' => $this->t('Yiddish'),
      'yo_NG' => $this->t('Yoruba'),
      'zh_CN' => $this->t('Simplified Chinese (China)'),
      'zh_HK' => $this->t('Traditional Chinese (Hong Kong)'),
      'zh_TW' => $this->t('Traditional Chinese (Taiwan)'),
      'zu_ZA' => $this->t('Zulu'),
      'zz_TR' => $this->t('Zazaki'),
    ];
  }

}
