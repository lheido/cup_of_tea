<?php

namespace Drupal\cup_of_tea\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for Cup of tea command plugins.
 */
interface CupOfTeaCommandInterface extends PluginInspectionInterface {

  /**
   * Return the plugin label.
   *
   * @return string
   *   The plugin label.
   */
  public function label();

  /**
   * Return the data for the auto-complete widget.
   *
   * @return \Drupal\cup_of_tea\CupOfTeaCommandItem[]
   *   A CupOfTeaCommandItem array.
   */
  public function data();

  /**
   * Add settings form to the Cup of tea settings page.
   *
   * @param array $form
   *   The settings form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function settingsForm(array &$form, FormStateInterface $form_state);

  /**
   * Handle the settings form submit.
   *
   * @param array $form
   *   The settings form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function submitForm(array &$form, FormStateInterface $form_state);

}
