<?php

namespace Drupal\poll;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access control handler for the poll entity.
 *
 * @see \Drupal\poll\Entity\Poll
 */
class PollAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // Allow view access if the user has the access polls permission.
    if ($operation == 'view' && $account->hasPermission('access polls')) {
      return AccessResult::allowedIfHasPermission($account, $account->hasPermission('access polls'));
    }

    // Otherwise fall back to the parent which checks the administer polls
    // permission.
    return parent::checkAccess($entity, $operation, $account);
  }

}
