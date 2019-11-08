<?php

namespace Drupal\social_media\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ForwardEmailForm.
 *
 */
class ForwardEmailForm extends FormBase {

  protected $configFactory;

  /**
   * The request object.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The mail manager
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;


  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack object.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RequestStack $request_stack, MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, LoggerInterface $logger) {
    $this->configFactory = $config_factory;
    $this->requestStack = $request_stack;
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('request_stack'),
      $container->get('plugin.manager.mail'),
      $container->get('language_manager'),
      $container->get('logger.factory')->get('action')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'forward_email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address'),
      '#required' => TRUE,
      '#description' => $this->t('The person email address whom you want to send'),
    ];

    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#required' => TRUE,
      '#default_value' => $this->requestStack->getCurrentRequest()
        ->get('subject')
    ];

    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Body'),
      '#required' => TRUE,
      '#default_value' => $this->requestStack->getCurrentRequest()->get('body')
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send')
    ];

    return $form;
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

    $recipient = $form_state->getValue('email');
    $params['message'] = $form_state->getValue('body');
    $params['subject'] = $form_state->getValue('subject');
    $langcode = $this->languageManager->getCurrentLanguage()->getId();

    $result = $this->mailManager->mail('social_media', 'forward_email', $recipient, $langcode, $params, NULL, TRUE);
    if ($result['result'] !== TRUE) {
      $this->logger->notice('Sent email to %recipient', ['%recipient' => $recipient]);
      drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
    }
    else {
      $this->logger->notice('Sent email to %recipient', ['%recipient' => $recipient]);
      drupal_set_message($this->t('Your message has been send to @email', ['@email' => $recipient]));
    }
  }
}
