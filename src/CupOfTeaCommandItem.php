<?php

namespace Drupal\cup_of_tea;

/**
 * Class CupOfTeaCommandItem.
 *
 * @package Drupal\cup_of_tea
 */
class CupOfTeaCommandItem {

  /**
   * The command item label.
   *
   * @var string
   */
  public $label;

  /**
   * The command item link.
   *
   * @var string
   */
  public $link;

  /**
   * The command item group.
   *
   * @var string
   */
  public $group;

  /**
   * CupOfTeaCommandItem constructor.
   *
   * @param string $label
   *   The command item label.
   * @param string $link
   *   The command link.
   * @param string $group
   *   The command group.
   */
  public function __construct(string $label, string $link, string $group = '') {
    $this->label = $label;
    $this->link = $link;
    $this->group = $group;
  }

}
