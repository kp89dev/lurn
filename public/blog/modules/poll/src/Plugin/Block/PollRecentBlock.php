<?php

namespace Drupal\poll\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Most recent poll' block.
 *
 * @Block(
 *   id = "poll_recent_block",
 *   admin_label = @Translation("Most recent poll"),
 *   category = @Translation("Lists (Views)")
 * )
 */
class PollRecentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access polls');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return array('poll_list');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $polls = \Drupal::entityManager()->getStorage('poll')->getMostRecentPoll();
    if ($polls) {
      $poll = reset($polls);
      // If we're viewing this poll, don't show this block.
//      $page = \Drupal::request()->attributes->get('poll');
//      if ($page instanceof PollInterface && $page->id() == $poll->id()) {
//        return;
//      }
      // @todo: new view mode using ajax
      $output = entity_view($poll, 'block');
      $output['#title'] = $poll->label();
      return $output;
    }
  }
}
