<?php

namespace Drupal\cup_of_tea\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * @file
 * Form to render the cup of tea front form.
 */

/**
 * Class CupOfTeaForm.
 *
 * @package Drupal\cup_of_tea\Form
 */
class CupOfTeaForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'cup_of_tea_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['cup_of_tea'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Navigate to'),
      '#title_display' => 'visualy-hidden',
      '#attributes' => [
        'data-autocomplete' => '',
        'placeholder' => $this->t('Navigate to'),
      ],
      '#suffix' => '<div data-autocomplete-results style="position: relative;"></div>',
    ];
    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}