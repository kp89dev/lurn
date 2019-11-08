<?php

namespace Drupal\social_media\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\social_media\Event\SocialMediaEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class SocialMediaAdminForm.
 *
 */
class SocialMediaAdminForm extends ConfigFormBase {

  /**
   * An event dispatcher instance to use for configuration events.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * {@inheritdoc}
   */
  public function __construct(
  ConfigFactoryInterface $config_factory, EventDispatcherInterface $event_dispatcher) {
    parent::__construct($config_factory);
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'), $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'social_media.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_media_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('social_media.settings');
    $social_medias = $this->getSocialMedias();
    foreach ($social_medias as $key => $label) {
      $form[$key] = array(
        '#type' => 'details',
        '#title' => t('@social_media settings', array('@social_media' => $label)),
        '#open' => TRUE,
      );
      $form[$key][$key . '_enable'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable'),
        '#default_value' => $config->get('social_media.' . $key . '.enable'),
        '#description' => t('Globally disabled the settings')
      ];

      $form[$key][$key . '_api_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('API url'),
        '#default_value' => $config->get('social_media.' . $key . '.api_url'),
      ];

      // Handle some extra configuration for email service.
      if($key == 'email'){

        $form[$key][$key . '_api_url']['#states'] = [
          'invisible' => [
            ':input[name="'.$key . '_enable_forward'.'"]' => array('checked' => TRUE),
          ],
        ];

        $form[$key][$key . '_enable_forward'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Enable forward instead for client email service'),
          '#default_value' => $config->get('social_media.' . $key . '.enable_forward'),
          '#description' => t('If it is checked then forward email form will open as model dialog.')
        ];
        $form[$key][$key . '_show_forward'] = [
          '#type' => 'radios',
          '#options' => [1 => $this->t('Model dialog'),2 => $this->t('Separate page')],
          '#title' => $this->t('Choose how you want to show forward email form'),
          '#default_value' => $config->get('social_media.' . $key . '.show_forward')?$config->get('social_media.' . $key . '.show_forward'):1,
          '#states' => [
            'visible' => [
              ':input[name="'.$key . '_enable_forward'.'"]' => array('checked' => TRUE),
             ],
            ],
          '#description' => t('default set as dialog popup, you can change it to show in separate page')
        ];
      }

      $form[$key][$key . '_api_event'] = [
        '#type' => 'select',
        '#title' => $this->t('Event'),
        '#options' => array('href' => 'href', 'onclick' => 'onclick'),
        '#default_value' => $config->get('social_media.' . $key . '.api_event'),
      ];

      $form[$key][$key . '_drupalSettings'] = [
        '#type' => 'textarea',
        '#title' => $this->t('drupalSettings variables'),
        '#default_value' => $config->get('social_media.' . $key . '.drupalSettings'),
        '#description' => t('Defines different drupalSettings variable.Each settings in new line.eg:<br/>application_id|343434434<br/>' .
          'you can get those variables in js.eg drupalSettings.social_media.application_id')
      ];

      $form[$key][$key . '_library'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Drupal library'),
        '#default_value' => $config->get('social_media.' . $key . '.library'),
        '#description' => t('Add drupal custom library.eg: social_media/facebook')
      ];


      $form[$key][$key . '_text'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link text'),
        '#default_value' => $config->get('social_media.' . $key . '.text'),
        '#description' => t('Text of the link')
      ];

      $form[$key][$key . '_default_img'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Default image'),
        '#default_value' => $config->get('social_media.' . $key . '.default_img'),
        '#description' => t('If it is checked default image will be loaded. Make service name with icon name. eg:facebook_share.svg')
      ];
      $form[$key][$key . '_img'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Alternative image link'),
        '#states' => array('visible' => array(
            ':input[name="' . $key . '_default_img' . '"]' => array('checked' => FALSE),
          )),
        '#default_value' => $config->get('social_media.' . $key . '.img'),
        '#description' => t('If you want to have your custom image, give image path.')
      ];
      $form[$key][$key . '_weight'] = [
        '#type' => 'number',
        '#title' => $this->t('Order of share button'),
        '#max' => 10,
        '#min' => 0,
        '#default_value' => $config->get('social_media.' . $key . '.weight'),
        '#description' => t('Order of the share link to render')
      ];

      $form[$key][$key . '_attributes'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Attributes'),
        '#default_value' => $config->get('social_media.' . $key . '.attributes'),
        '#description' => t('Defines different attributes of link. Each attribute in new line.eg:<br/>target|blank<br/>'
          . 'class|facebook-share js-share')
      ];
    }
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

    $socials = $this->getSocialMedias();
    $config = $this->config('social_media.settings');
    $config->clear('social_media.settings');
    foreach ($socials as $social => $media) {
      if ($form_state->hasValue($social . '_enable')) {
        $config->set('social_media.' . $social . '.enable', $form_state->getValue($social . '_enable'));
      }
      if ($form_state->hasValue($social . '_api_url')) {
        $config->set('social_media.' . $social . '.api_url', $form_state->getValue($social . '_api_url'));
      }
      if ($form_state->hasValue($social . '_api_event')) {
        $config->set('social_media.' . $social . '.api_event', $form_state->getValue($social . '_api_event'));
      }
      if ($form_state->hasValue($social . '_drupalSettings')) {
        $config->set('social_media.' . $social . '.drupalSettings', $form_state->getValue($social . '_drupalSettings'));
      }
      if ($form_state->hasValue($social . '_library')) {
        $config->set('social_media.' . $social . '.library', $form_state->getValue($social . '_library'));
      }
      if ($form_state->hasValue($social . '_text')) {
        $config->set('social_media.' . $social . '.text', $form_state->getValue($social . '_text'));
      }
      if ($form_state->hasValue($social . '_default_img')) {
        $config->set('social_media.' . $social . '.default_img', $form_state->getValue($social . '_default_img'));
      }
      if ($form_state->hasValue($social . '_img')) {
        $config->set('social_media.' . $social . '.img', $form_state->getValue($social . '_img'));
      }
      if ($form_state->hasValue($social . '_weight')) {
        $config->set('social_media.' . $social . '.weight', $form_state->getValue($social . '_weight'));
      }
      if ($form_state->hasValue($social . '_attributes')) {
        $config->set('social_media.' . $social . '.attributes', $form_state->getValue($social . '_attributes'));
      }
      if ($form_state->hasValue($social . '_enable_forward')) {
        $config->set('social_media.' . $social . '.enable_forward', $form_state->getValue($social . '_enable_forward'));
      }
      if ($form_state->hasValue($social . '_show_forward')) {
        $config->set('social_media.' . $social . '.show_forward', $form_state->getValue($social . '_show_forward'));
      }
    }
    $config->save();
    drupal_set_message($this->t('Your configuration has been saved'));
  }

  /**
   *
   * @return type
   */
  protected function getSocialMedias() {
    $element = array(
      'facebook_share' => 'Facebook share',
      'facebook_msg' => 'Facebook messenger',
      'linkedin' => 'Linkedin',
      'twitter' => 'Twitter',
      'google_plus' => 'Google Plus',
      'email' => 'Email',
    );

    $event = new SocialMediaEvent($element);
    $this->eventDispatcher->dispatch('social_media.add_more_link', $event);
    $element = $event->getElement();

    return $element;
  }

}
