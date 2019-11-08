<?php

namespace Drupal\poll;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;

/**
 * Controller class for polls.
 *
 * This extends the default content entity storage class,
 * adding required special handling for poll entities.
 */
class PollStorage extends SqlContentEntityStorage implements PollStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function getTotalVotes(PollInterface $poll) {
    return \Drupal::service('poll_vote.storage')->getTotalVotes($poll);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteVotes(PollInterface $poll) {
    return \Drupal::service('poll_vote.storage')->deleteVotes($poll);
  }

  /**
   * {@inheritdoc}
   */
  public function getUserVote(PollInterface $poll) {
    return \Drupal::service('poll_vote.storage')->getUserVote($poll);
  }

  /**
   * {@inheritdoc}
   */
  public function saveVote(array $options) {
    return \Drupal::service('poll_vote.storage')->saveVote($options);
  }

  /**
   * {@inheritdoc}
   */
  public function getVotes(PollInterface $poll) {
    return \Drupal::service('poll_vote.storage')->getVotes($poll);
  }

  /**
   * {@inheritdoc}
   */
  public function cancelVote(PollInterface $poll, AccountInterface $account = NULL) {
    \Drupal::service('poll_vote.storage')->cancelVote($poll, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function getPollDuplicates(PollInterface $poll) {
    $query = \Drupal::entityQuery('poll');
    $query->condition('question', $poll->label());

    if ($poll->id()) {
      $query->condition('id', $poll->id(), '<>');
    }
    return $this->loadMultiple($query->execute());
  }

  /**
   * {@inheritdoc}
   */
  public function getMostRecentPoll() {
    $query = \Drupal::entityQuery('poll')
      ->condition('status', POLL_PUBLISHED)
      ->sort('created', 'DESC')
      ->pager(1);
    return $this->loadMultiple($query->execute());
  }

  /**
   * {@inheritdoc}
   */
  public function getExpiredPolls() {
    $query = $this->database->query("SELECT id FROM {poll_field_data} WHERE (UNIX_TIMESTAMP() > (created + runtime)) AND status = 1 AND runtime <> 0");
    return $this->loadMultiple($query->fetchCol());
  }
}
