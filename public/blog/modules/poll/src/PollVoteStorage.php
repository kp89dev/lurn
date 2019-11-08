<?php

namespace Drupal\poll;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountInterface;

/**
 * Controller class for poll vote storage.
 */
class PollVoteStorage implements PollVoteStorageInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * Constructs a new PollVoteStorage.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   A Database connection to use for reading and writing database data.
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cache_tags_invalidator
   *   The cache tags invalidator.
   */
  public function __construct(Connection $connection, CacheTagsInvalidatorInterface $cache_tags_invalidator) {
    $this->connection = $connection;
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteChoicesVotes(array $choices) {
    $this->connection->delete('poll_vote')
      ->condition('chid', $choices, 'IN')
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteVotes(PollInterface $poll) {
    $this->connection->delete('poll_vote')->condition('pid', $poll->id())
    ->execute();

    // Deleting a vote means that any cached vote might not be updated in the
    // UI, so we need to invalidate them all.
    $this->cacheTagsInvalidator->invalidateTags(['poll-votes:' . $poll->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function cancelVote(PollInterface $poll, AccountInterface $account = NULL) {
    if ($account->id()) {
      $this->connection->delete('poll_vote')
        ->condition('pid', $poll->id())
        ->condition('uid', $account->id())
        ->execute();
    }
    else {
      $this->connection->delete('poll_vote')
        ->condition('pid', $poll->id())
        ->condition('uid', \Drupal::currentUser()->id())
        ->condition('hostname', \Drupal::request()->getClientIp())
        ->execute();
    }

    // Deleting a vote means that any cached vote might not be updated in the
    // UI, so we need to invalidate them all.
    $this->cacheTagsInvalidator->invalidateTags(['poll-votes:' . $poll->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function saveVote(array $options) {
    if (!is_array($options)) {
      return;
    }
    $this->connection->insert('poll_vote')->fields($options)->execute();

    // Deleting a vote means that any cached vote might not be updated in the
    // UI, so we need to invalidate them all.
    $this->cacheTagsInvalidator->invalidateTags(['poll-votes:' . $options['pid']]);
  }

  /**
   * {@inheritdoc}
   */
  public function getVotes(PollInterface $poll) {
    $votes = array();
    // Set votes for all options to 0
    $options = $poll->getOptions();
    foreach ($options as $id => $label) {
      $votes[$id] = 0;
    }

    $result = $this->connection->query("SELECT chid, COUNT(chid) AS votes FROM {poll_vote} WHERE pid = :pid GROUP BY chid", array(':pid' => $poll->id()));
    // Replace the count for options that have recorded votes in the database.
    foreach ($result as $row) {
      $votes[$row->chid] = $row->votes;
    }

    return $votes;
  }

  /**
   * {@inheritdoc}
   */
  public function getUserVote(PollInterface $poll) {
    $uid = \Drupal::currentUser()->id();
    if ($uid || $poll->getAnonymousVoteAllow()) {
      if ($uid) {
        $query = $this->connection->query("SELECT * FROM {poll_vote} WHERE pid = :pid AND uid = :uid", array(
          ':pid' => $poll->id(),
          ':uid' => $uid
        ));
      }
      else {
        $query = $this->connection->query("SELECT * FROM {poll_vote} WHERE pid = :pid AND hostname = :hostname AND uid = 0", array(
          ':pid' => $poll->id(),
          ':hostname' => \Drupal::request()->getClientIp()
        ));
      }
      return $query->fetchAssoc();
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getTotalVotes(PollInterface $poll) {
    $query = $this->connection->query("SELECT COUNT(chid) FROM {poll_vote} WHERE pid = :pid", array(':pid' => $poll->id()));
    return $query->fetchField();
  }

}
