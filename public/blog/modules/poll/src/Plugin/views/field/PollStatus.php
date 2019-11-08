<?php

namespace Drupal\poll\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;


/**
 * Field handler which displays the flag indicating whether the poll is active
 * including the runtime.
 *
 * @ViewsField("poll_status")
 */
class PollStatus extends FieldPluginBase {

  /**
   * @param \Drupal\views\ResultRow $values
   * @return mixed
   */
  function render(ResultRow $values) {
    $pollStorage = \Drupal::entityManager()->getStorage('poll');
    $entity = $values->_entity;

    if ($entity->isOpen() && $entity->getRuntime() != 0) {
      $date = \Drupal::service('date.formatter')->format($entity->getCreated() + $entity->getRuntime(), 'short');
      $output = 'Yes (until ' . rtrim(strstr($date, '-', true)) . ')';
    }
    else if ($entity->isOpen()) {
      $output = t('Yes');
    }
    else {
     $output = 'No';
    }

    return $output;
  }
}
