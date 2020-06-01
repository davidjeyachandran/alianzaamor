<?php

/**
 * @file
 * Contains Drupal\mecrawl\Form\SettingsForm.
 */

namespace Drupal\aa_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class AaCoreSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'aa_core.settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'aa_core_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['delivery_confirm'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Confirm'),
      '#description' => $this->t('Link text to confirm delivery.'),
      '#default_value' => $config->get('delivery.confirm'),
    ];

    $form['delivery_reject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Reject'),
      '#description' => $this->t('Link text to confirm rejection.'),
      '#default_value' => $config->get('delivery.reject'),
    ];

    $form['delivery_confirmed'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Confirmed'),
      '#description' => $this->t('Text to to specify that delivery was confirmed.'),
      '#default_value' => $config->get('delivery.confirmed'),
    ];

    $form['delivery_rejected'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rejected'),
      '#description' => $this->t('Text to to specify that delivery was rejected.'),
      '#default_value' => $config->get('delivery.rejected'),
    ];

    $form['delivery_confirmed_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Confirmed message'),
      '#description' => $this->t('Message to appear after user confirmed the pickup.'),
      '#default_value' => $config->get('delivery.confirmed_message'),
    ];

    $form['delivery_rejected_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rejected message'),
      '#description' => $this->t('Message to appear after user rejected the pickup.'),
      '#default_value' => $config->get('delivery.rejected_message'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('delivery.confirm', $form_state->getValue('delivery_confirm'))
      ->set('delivery.confirmed', $form_state->getValue('delivery_confirmed'))
      ->set('delivery.reject', $form_state->getValue('delivery_reject'))
      ->set('delivery.rejected', $form_state->getValue('delivery_rejected'))
      ->set('delivery.confirmed_message', $form_state->getValue('delivery_confirmed_message'))
      ->set('delivery.rejected_message', $form_state->getValue('delivery_rejected_message'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
