<?php

/**
 * @file
 * Contains Drupal\mecrawl\Form\SettingsForm.
 */

namespace Drupal\aa_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DomCrawler\Crawler;
use Drupal\Core\Link;

class AaCoreSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'aa_core.settings',
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
    $config = $this->config('aa_core.settings');

    $form['delivery.confirm'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Confirm'),
      '#description' => $this->t('Link text to confirm delivery.'),
      '#default_value' => $config->get('delivery.confirm'),
    ];

    $form['delivery.reject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Confirm'),
      '#description' => $this->t('Link text to confirm delivery.'),
      '#default_value' => $config->get('delivery.reject'),
    ];

    $form['delivery.confirmed'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Confirmed'),
      '#description' => $this->t('Text to to specify that delivery was confirmed.'),
      '#default_value' => $config->get('delivery.confirmed'),
    ];

    $form['delivery.rejected'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rejected'),
      '#description' => $this->t('Text to to specify that delivery was rejected.'),
      '#default_value' => $config->get('delivery.rejected'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  /*public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('mecrawl.settings');

    $config
      ->set('crawl_url', $form_state->getValue('crawl_url'))
      ->save();
  }*/

}
