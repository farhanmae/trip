<?php

namespace Drupal\Tests\hook_event_dispatcher\Unit\Manager;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class FakeEvent.
 *
 * @package Drupal\Tests\hook_event_dispatcher\Unit\Manager
 */
class FakeEvent extends Event implements EventInterface {

  /**
   * FakeEvent constructor.
   *
   * @param string $dispatcherType
   *   Dispatcher type.
   */
  public function __construct(private $dispatcherType) {
  }

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return $this->dispatcherType;
  }

}
