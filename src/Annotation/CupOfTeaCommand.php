<?php

namespace Drupal\cup_of_tea\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Cup of tea command item annotation object.
 *
 * @see \Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager
 * @see plugin_api
 *
 * @Annotation
 */
class CupOfTeaCommand extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
