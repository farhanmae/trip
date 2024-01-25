<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Event\EventInterface;

abstract class EntityBundleEventBase extends Event implements EventInterface {

  /**
   * EntityBundleCreateEvent constructor.
   *
   * @param string $entity_type_id
   *   The type of $entity; e.g. 'node' or 'user'.
   * @param string $bundle
   *   The name of the bundle.
   */
  public function __construct(protected string $entity_type_id, protected string $bundle) {
  }

  /**
   * Gets the type of the entity.
   *
   * @return string
   */
  public function getEntityTypeId(): string {
    return $this->entity_type_id;
  }

  /**
   * Gets the bundle of the entity.
   *
   * @return string
   */
  public function getBundle(): string {
    return $this->bundle;
  }

}
