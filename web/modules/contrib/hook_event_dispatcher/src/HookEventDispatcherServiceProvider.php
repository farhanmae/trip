<?php

namespace Drupal\hook_event_dispatcher;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Defines a service provider for the Hook Event Dispatcher module.
 */
class HookEventDispatcherServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container): void {
    if (version_compare(\Drupal::VERSION, '9.4', '<') && $container->hasDefinition('hook_event_dispatcher.module_handler')) {
      $container->removeDefinition('hook_event_dispatcher.module_handler');
    }
  }

}
