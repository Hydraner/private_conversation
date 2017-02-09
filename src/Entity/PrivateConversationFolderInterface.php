<?php

namespace Drupal\private_conversation\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Folder entities.
 *
 * @ingroup private_conversation
 */
interface PrivateConversationFolderInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Folder name.
   *
   * @return string
   *   Name of the Folder.
   */
  public function getName();

  /**
   * Sets the Folder name.
   *
   * @param string $name
   *   The Folder name.
   *
   * @return \Drupal\private_conversation\Entity\PrivateConversationFolderInterface
   *   The called Folder entity.
   */
  public function setName($name);

  /**
   * Gets the Folder creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Folder.
   */
  public function getCreatedTime();

  /**
   * Sets the Folder creation timestamp.
   *
   * @param int $timestamp
   *   The Folder creation timestamp.
   *
   * @return \Drupal\private_conversation\Entity\PrivateConversationFolderInterface
   *   The called Folder entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the weight of this folder.
   *
   * @return int
   *   The weight of the folder.
   */
  public function getWeight();

  /**
   * Gets the weight of this folder.
   *
   * @param int $weight
   *   The folder's weight.
   *
   * @return $this
   */
  public function setWeight($weight);
}
