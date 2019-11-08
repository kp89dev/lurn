<?php

namespace Drupal\social_media\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class SocialMediaEvent
 */
class SocialMediaEvent extends Event {

  /**
   * @var array.
   */
  protected $element;

  /**
   * Constructor.
   */
  public function __construct($element) {
    $this->element = $element;
  }

  /**
   * Return the element.
   * @return array()
   */
  public function getElement() {
    return $this->element;
  }

  /**
   */
  public function setElement($element) {
    $this->element = $element;
  }

}
