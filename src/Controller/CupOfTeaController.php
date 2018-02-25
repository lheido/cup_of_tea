<?php

namespace Drupal\cup_of_tea\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Access\AccessManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\LocalTaskManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

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
   * The \Symfony\Component\Serializer\SerializerInterface service.
   *
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * Constructs a new CupOfTeaController object.
   *
   * @param \Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager $cup_of_tea_command_manager
   *   The cup of tea plugin manager service.
   * @param \Drupal\Core\Access\AccessManagerInterface $access_manager
   *   The access manager service.
   * @param \Symfony\Component\Serializer\SerializerInterface $serializer
   *   The serializer service.
   */
  public function __construct(CupOfTeaCommandManager $cup_of_tea_command_manager, AccessManagerInterface $access_manager, SerializerInterface $serializer) {
    $this->cupOfTeaCommandManager = $cup_of_tea_command_manager;
    $this->accessManager = $access_manager;
    $this->serializer = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.cup_of_tea_command'),
      $container->get('access_manager'),
      $container->get('serializer')
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
