<?php

namespace Drupal\private_conversation;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Overrides the private_conversation services.
 */
class PrivateConversationServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Use a custom EntityAutocompleteMatcher.
    $definition = $container->getDefinition('entity.autocomplete_matcher');
    $definition->setClass('Drupal\private_conversation\Entity\EntityAutocompleteMatcher');
  }

}
