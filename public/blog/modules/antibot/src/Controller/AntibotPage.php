<?php

/**
 * @file
 * Contains \Drupal\antibot\Controller\AntibotPage.
 */

namespace Drupal\antibot\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class AntibotPage.
 *
 * @package Drupal\antibot\Controller
 */
class AntibotPage extends ControllerBase {
  /**
   * The Antibot page where robotic form submissions end up.
   *
   * @return string
   *   Return Hello string.
   */
  public function page() {
    $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
    $page = [];
    $page['message'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['antibot-message', 'antibot-message-error'],
      ],
      '#value' => $this->t('You have reached this page because you submitted a form that required Javascript to be enabled on your browser. This protection is in place to attempt to prevent automated submissions made on forms. Please return to the page that you came from and enable Javascript on your browser before attempting to submit the form again.'),
    ];
    $page['return'] = [
      '#type' => 'link',
      '#title' => $this->t('Click here to go back'),
      '#url' => $referer ? Url::fromUri($referer) : NULL,
      '#access' => (bool) $referer,
    ];
    $page['#attached']['library'][] = 'antibot/antibot.form';
    return $page;
  }

}
