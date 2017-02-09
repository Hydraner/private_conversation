<?php

namespace Drupal\private_conversation\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default argument plugin to extract a folder ID.
 *
 * @ViewsArgumentDefault(
 *   id = "folder_id_from_url",
 *   title = @Translation("Folder ID from URL")
 * )
 */
class FolderIdFromUrl extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {

  /**
   * The folder entity from the route.
   *
   * @var \Drupal\private_conversation\Entity\PrivateConversationFolder
   */
  protected $folder;

  /**
   * Constructs a new FolderIdFromUrl instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Plugin\Context\ContextProviderInterface $context_provider
   *   The folder route context.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ContextProviderInterface $context_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    /** @var \Drupal\Core\Plugin\Context\ContextInterface[] $contexts */
    $contexts = $context_provider->getRuntimeContexts(['private_conversation_folder']);
    $this->folder = $contexts['private_conversation_folder']->getContextValue();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('private_conversation.folder_route_context')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getArgument() {
    if (!empty($this->folder) && $id = $this->folder->id()) {
      return [$id => $this->folder->label()];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // We cache the result on the route instead of the URL so that path aliases
    // can all use the same cache context. If you look at ::getArgument() you'll
    // see that we actually get the folder ID from the route, not the URL.
    return ['route'];
  }

}
