<?php

namespace Drupal\cup_of_tea\Plugin\CupOfTeaCommand;

use Drupal\Core\Access\AccessManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\LocalTaskManagerInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\Url;
use Drupal\cup_of_tea\CupOfTeaCommandItem;
use Drupal\cup_of_tea\Plugin\CupOfTeaCommandBase;
use Drupal\system\Entity\Menu;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @file
 * Default cup of tea command.
 */

/**
 * Class DefaultCupOfTeaCommand.
 *
 * @CupOfTeaCommand(
 *   id = "cup_of_tea_default_command",
 *   label = @Translation("Default cup of tea command")
 * )
 */
class DefaultCupOfTeaCommand extends CupOfTeaCommandBase implements ContainerFactoryPluginInterface {

  /**
   * The \Drupal\Core\Access\AccessManagerInterface service.
   *
   * @var \Drupal\Core\Access\AccessManagerInterface
   */
  protected $accessManager;

  /**
   * The \Drupal\Core\Config\ConfigFactoryInterface service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The \Drupal\Core\Menu\MenuLinkTreeInterface service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuLinkTree;

  /**
   * The \Drupal\Core\Menu\LocalTaskManagerInterface service.
   *
   * @var \Drupal\Core\Menu\LocalTaskManagerInterface
   */
  protected $pluginManagerMenuLocalTask;

  /**
   * The \Drupal\Core\Routing\RouteProvider service.
   *
   * @var \Drupal\Core\Routing\RouteProvider
   */
  protected $routeProvider;

  /**
   * DefaultCupOfTeaCommand constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Access\AccessManagerInterface $access_manager
   *   The access manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_link_tree
   *   The menu link tree service.
   * @param \Drupal\Core\Menu\LocalTaskManagerInterface $plugin_manager_menu_local_task
   *   The menu local task service.
   * @param \Drupal\Core\Routing\RouteProvider $route_provider
   *   The route provider service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessManagerInterface $access_manager, ConfigFactoryInterface $config_factory, MenuLinkTreeInterface $menu_link_tree, LocalTaskManagerInterface $plugin_manager_menu_local_task, RouteProvider $route_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->accessManager = $access_manager;
    $this->configFactory = $config_factory;
    $this->menuLinkTree = $menu_link_tree;
    $this->pluginManagerMenuLocalTask = $plugin_manager_menu_local_task;
    $this->routeProvider = $route_provider;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('access_manager'),
      $container->get('config.factory'),
      $container->get('menu.link_tree'),
      $container->get('plugin.manager.menu.local_task'),
      $container->get('router.route_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function data() {
    $config = $this->configFactory->get('cup_of_tea.settings');
    $selected_menus = $config->get('menus');
    /** @var \Drupal\cup_of_tea\CupOfTeaCommandItem[] $data */
    $data = [];
    foreach ($selected_menus as $selected_menu) {
      $data = array_merge($data, $this->buildData($selected_menu));
    }
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('cup_of_tea.settings');
    $form['menus'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Available menus'),
      '#description' => $this->t('Select the menus you want to navigate through.'),
      '#options' => $this->getMenuOptions(),
      '#default_value' => $config->get('menus') ?? [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('cup_of_tea.settings');
    $config->set('menus', $form_state->getValue('menus') ?? []);
  }

  /**
   * Get menu options from Menu::loadMultiple().
   *
   * @return array
   *   A array of menu names keyed by the menu machine name.
   */
  protected function getMenuOptions(): array {
    $menus = Menu::loadMultiple();
    return array_map(function (Menu $menu) {
      return $menu->label();
    }, $menus);
  }

  /**
   * Get the menu link tree (flatten) according to the menu name.
   *
   * Also include the local tasks.
   *
   * @param string $menu_name
   *   The menu machine name to load.
   *
   * @return \Drupal\cup_of_tea\CupOfTeaCommandItem[]
   *   An menu link array.
   */
  protected function buildData($menu_name) {
    /** @var \Drupal\cup_of_tea\CupOfTeaCommandItem[] $data */
    $data = [];
    $parameters = new MenuTreeParameters();
    $tree = $this->menuLinkTree->load($menu_name, $parameters);

    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
      ['callable' => 'menu.default_tree_manipulators:flatten'],
    ];

    $elements = $this->menuLinkTree->transform($tree, $manipulators);

    // Unset not allowed menu link element.
    foreach ($elements as $key => $element) {
      if (!$element->access->isAllowed()) {
        unset($tree[$key]);
      }
    }

    $route_match = \Drupal::routeMatch();

    /** @var \Drupal\Core\Menu\MenuLinkTreeElement $element */
    foreach ($elements as $element) {
      // Transform MenuLinkTreeElement => CupOfTeaCommandItem.
      $url = $element->link->getUrlObject();
      $link = $this->getLink($element->link->getRouteName(), $url);
      $data[$link] = new CupOfTeaCommandItem(
        $element->link->getTitle(),
        $link,
        $menu_name
      );
      // Get local tasks for allowed elements.
      $local_tasks = $this->pluginManagerMenuLocalTask->getLocalTasksForRoute($element->link->getRouteName());
      foreach ($local_tasks as $tasks) {
        /** @var \Drupal\Core\Menu\LocalTaskDefault $task */
        foreach ($tasks as $task) {
          $task_route = $task->getRouteName();
          // Comment from the Coffee module:
          // Merges the parent's route parameter with the child ones since you
          // calculate the local tasks outside of parent route context.
          $task_route_parameters = $task->getRouteParameters($route_match) + $element->link->getRouteParameters();
          if ($this->accessManager->checkNamedRoute($task_route, $task_route_parameters)) {
            $task_url = Url::fromRoute($task_route, $task_route_parameters);
            $task_link = $this->getLink($task_route, $task_url);
            if (empty($data[$task_link])) {
              $data[$task_link] = new CupOfTeaCommandItem(
                $task->getTitle(),
                $task_link,
                $element->link->getRouteName()
              );
            }
          }
        }
      }
    }

    return array_values($data);
  }

  /**
   * Generate the string url. take into account the _csrf_token requirements.
   *
   * @param string $route_name
   *   The route name.
   * @param \Drupal\Core\Url $url
   *   The url object.
   *
   * @return string
   *   The string url with right csrf token.
   */
  protected function getLink(string $route_name, Url $url) {
    if (!$url->isExternal()) {
      $route = $this->routeProvider->getRouteByName($route_name);
      if ($route->hasRequirement('_csrf_token') && !empty($route->getRequirement('_csrf_token'))) {
        $token = \Drupal::csrfToken()->get($url->getInternalPath());
        $url->setOption('query', ['token' => $token]);
      }
    }
    return $url->toString();
  }

}
