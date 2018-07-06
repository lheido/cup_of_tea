<?php

namespace Drupal\cup_of_tea\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Access\AccessManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CupOfTeaController.
 */
class CupOfTeaController extends ControllerBase {

  /**
   * The \Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager service.
   *
   * @var \Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager
   */
  protected $cupOfTeaCommandManager;

  /**
   * The \Drupal\Core\Access\AccessManagerInterface service.
   *
   * @var \Drupal\Core\Access\AccessManagerInterface
   */
  protected $accessManager;

  /**
   * Constructs a new CupOfTeaController object.
   *
   * @param \Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager $cup_of_tea_command_manager
   *   The cup of tea plugin manager service.
   * @param \Drupal\Core\Access\AccessManagerInterface $access_manager
   *   The access manager service.
   */
  public function __construct(CupOfTeaCommandManager $cup_of_tea_command_manager, AccessManagerInterface $access_manager) {
    $this->cupOfTeaCommandManager = $cup_of_tea_command_manager;
    $this->accessManager = $access_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.cup_of_tea_command'),
      $container->get('access_manager')
    );
  }

  /**
   * Autocomplete callback.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Return cup of tea data.
   */
  public function jsonData() {
    $data = [];
    $definitions = $this->cupOfTeaCommandManager->getDefinitions();
    foreach ($definitions as $definition) {
      /** @var \Drupal\cup_of_tea\Plugin\CupOfTeaCommandInterface $plugin */
      $plugin = $this->cupOfTeaCommandManager->createInstance($definition['id']);
      $data = array_merge($data, $plugin->data());
    }

    $data = array_map(function ($elt) {
      return (array) $elt;
    }, $data);

    return new JsonResponse($data);
  }

}
