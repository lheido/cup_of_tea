<?php

namespace Drupal\cup_of_tea\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager;

/**
 * Class CupOfTeaSettingsForm.
 */
class CupOfTeaSettingsForm extends ConfigFormBase {

  /**
   * The \Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager service.
   *
   * @var \Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager
   */
  protected $cupOfTeaCommandManager;

  /**
   * CupOfTeaSettingsForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\cup_of_tea\Plugin\CupOfTeaCommandManager $cup_of_tea_command_manager
   *   The cup of tea command service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, CupOfTeaCommandManager $cup_of_tea_command_manager) {
    parent::__construct($config_factory);
    $this->cupOfTeaCommandManager = $cup_of_tea_command_manager;
  }

  /**
   * {@inheritdoc}
   *
   * Override default create method to inject the cup of tea command service.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.cup_of_tea_command')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'cup_of_tea.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cup_of_tea_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('cup_of_tea.settings');

    $form['shortcut'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shortcut'),
      '#description' => $this->t('Shortcut to open the widget. Must be a valid Mousetrap keyboard combinations.'),
      '#default_value' => $config->get('shortcut') ?? 'alt+d',
    ];

    $definitions = $this->cupOfTeaCommandManager->getDefinitions();
    foreach ($definitions as $definition) {
      $plugin = $this->cupOfTeaCommandManager->createInstance($definition['id']);
      $plugin->settingsForm($form, $form_state);
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('cup_of_tea.settings');

    $config->set('shortcut', $form_state->getValue('shortcut'));
    $config->save();

    $definitions = $this->cupOfTeaCommandManager->getDefinitions();
    foreach ($definitions as $definition) {
      $plugin = $this->cupOfTeaCommandManager->createInstance($definition['id']);
      $plugin->submitForm($form, $form_state);
    }

  }

}
