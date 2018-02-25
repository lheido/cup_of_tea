<?php

namespace Drupal\cup_of_tea\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Cup of tea command plugin manager.
 */
class CupOfTeaCommandManager extends DefaultPluginManager {


  /**
   * Constructs a new CupOfTeaCommandManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/CupOfTeaCommand', $namespaces, $module_handler, 'Drupal\cup_of_tea\Plugin\CupOfTeaCommandInterface', 'Drupal\cup_of_tea\Annotation\CupOfTeaCommand');

    $this->alterInfo('cup_of_tea_cup_of_tea_command_info');
    $this->setCacheBackend($cache_backend, 'cup_of_tea_cup_of_tea_command_plugins');
  }

}
