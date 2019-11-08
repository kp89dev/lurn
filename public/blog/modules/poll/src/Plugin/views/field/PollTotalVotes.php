<?php

namespace Drupal\poll\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;


/**
 * Field handler which shows the total votes for a poll.
 *
 * @ViewsField("poll_totalvotes")
 */
class PollTotalVotes extends FieldPluginBase {

  /**
   * @param \Drupal\views\ResultRow $values
   * @return mixed
   */
  function render(ResultRow $values) {
    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    $entity = $values->_entity;
    $build['#markup'] = $vote_storage->getTotalVotes($entity);
    $build['#cache']['tags'][] = 'poll-votes:' . $entity->id();
    return $build;
  }
}
