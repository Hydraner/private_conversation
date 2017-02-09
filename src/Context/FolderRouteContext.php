<?php

namespace Drupal\private_conversation\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\private_conversation\Entity\PrivateConversationFolder;
use Drupal\private_conversation\Entity\PrivateConversationFolderInterface;

/**
 * Sets the current folder as a context on folder routes.
 */
class FolderRouteContext implements ContextProviderInterface {

  use StringTranslationTrait;

  /**
   * Constructs a new FolderRouteContext.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $current_route_match
   *   The current route match object.
   */
  public function __construct(RouteMatchInterface $current_route_match) {
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntimeContexts(array $unqualified_context_ids) {
    // Create an optional context definition for folder entities.
    $context_definition = new ContextDefinition('entity:private_conversation_folder', NULL, FALSE);

    // Cache this context on the route.
    $cacheability = new CacheableMetadata();
    $cacheability->setCacheContexts(['route']);

    // Create a context from the definition and retrieved or created folder.
    $context = new Context($context_definition, $this->getFolderFromRoute());
    $context->addCacheableDependency($cacheability);

    return ['private_conversation_folder' => $context];
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailableContexts() {
    $context = new Context(new ContextDefinition('entity:private_conversation_folder', $this->t('Folder from URL')));
    return ['private_conversation_folder' => $context];
  }

  /**
   * Retrieves the folder entity from the current route.
   *
   * This will try to load the folder entity from the route if present. If we
   * are on the folder add form, it will return a new folder entity.
   *
   * @return \Drupal\group\Entity\GroupInterface|null
   *   A group entity if one could be found or created, NULL otherwise.
   */
  public function getFolderFromRoute() {
    // See if the route has a folder parameter and try to retrieve it.
    if (($folder = $this->currentRouteMatch->getParameter('private_conversation_folder')) && $folder instanceof PrivateConversationFolderInterface) {
      return $folder;
    }
    // Create a new folder to use as context if on the group add form.
    elseif ($this->currentRouteMatch->getRouteName() == 'entity.private_conversation_folder.add_form') {
      return PrivateConversationFolder::create();
    }

    return NULL;
  }

}
