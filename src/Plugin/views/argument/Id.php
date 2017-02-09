<?php

namespace Drupal\private_conversation\Plugin\views\argument;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\views\Plugin\views\argument\NumericArgument;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Argument handler to accept a folderid.
 *
 * @ViewsArgument("folder_id")
 */
class Id extends NumericArgument {

  /**
   * The folder storage.
   *
   * @var \Drupal\private_conversation\Entity\PrivateConversationFolder
   */
  protected $folderStorage;

  /**
   * Constructs the Nid object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\Sql\SqlContentEntityStorage $folder_storage
   *   The folder storage.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SqlContentEntityStorage $folder_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->folderStorage = $folder_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')->getStorage('private_conversation_folder')
    );
  }

  /**
   * Override the behavior of title(). Get the title of the folder.
   */
  public function titleQuery() {
    $titles = array();

    $folders = $this->folderStorage->loadMultiple($this->value);
    foreach ($folders as $folder) {
      $titles[] = $folder->label();
    }
    return $titles;
  }

}
