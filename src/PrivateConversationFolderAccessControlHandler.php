<?php

namespace Drupal\private_conversation;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Folder entity.
 *
 * @see \Drupal\private_conversation\Entity\PrivateConversationFolder.
 */
class PrivateConversationFolderAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // Allow access if the user is the owner.
    if ($entity->getOwner()->id() == $account->id()) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    // Everyone is allowed to create folders.
    return AccessResult::allowed();
  }

}
