<?php

namespace Drupal\poll;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines a common interface for poll entity controller classes.
 */
interface PollStorageInterface extends EntityStorageInterface {

  /**
   * Save a user's vote.
   *
   * @param array $options
   *
   * @return mixed
   *
   * @deprecated in Drupal 8.x-1.0.
   *   Use \Drupal\poll\PollVoteStorageInterface::saveVote() instead.
   *
   * @see \Drupal\poll\PollVoteStorageInterface::saveVote()
   */
  public function saveVote(array $options);

  /**
   * Cancel a user's vote.
   *
   * @param PollInterface $poll
   * @param AccountInterface $account
   *
   * @return mixed
   *
   * @deprecated in Drupal 8.x-1.0.
   *   Use \Drupal\poll\PollVoteStorageInterface::cancelVote() instead.
   *
   * @see \Drupal\poll\PollVoteStorageInterface::cancelVote()
   */
  public function cancelVote(PollInterface $poll, AccountInterface $account = NULL);

  /**
   * Get total votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   *
   * @deprecated in Drupal 8.x-1.0.
   *   Use \Drupal\poll\PollVoteStorageInterface::getTotalVotes() instead.
   *
   * @see \Drupal\poll\PollVoteStorageInterface::getTotalVotes()
   */
  public function getTotalVotes(PollInterface $poll);

  /**
   * Get all votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   *
   * @deprecated in Drupal 8.x-1.0.
   *   Use \Drupal\poll\PollVoteStorageInterface::getVotes() instead.
   *
   * @see \Drupal\poll\PollVoteStorageInterface::getVotes()
   */
  public function getVotes(PollInterface $poll);

  /**
   * Delete a user's votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   *
   * @deprecated in Drupal 8.x-1.0.
   *   Use \Drupal\poll\PollVoteStorageInterface::deleteVotes() instead.
   *
   * @see \Drupal\poll\PollVoteStorageInterface::deleteVotes()
   */
  public function deleteVotes(PollInterface $poll);

  /**
   * Get a user's votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   *
   * @deprecated in Drupal 8.x-1.0.
   *   Use \Drupal\poll\PollVoteStorageInterface::getUserVote() instead.
   *
   * @see \Drupal\poll\PollVoteStorageInterface::getUserVote()
   */
  public function getUserVote(PollInterface $poll);

  /**
   * Get the most recent poll posted on the site.
   *
   * @return mixed
   */
  public function getMostRecentPoll();

  /**
   * Find all duplicates of a poll by matching the question.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   */
  public function getPollDuplicates(PollInterface $poll);

  /**
   * Returns all expired polls.
   *
   * @return \Drupal\poll\PollInterface[]
   *
   */
  public function getExpiredPolls();

}
