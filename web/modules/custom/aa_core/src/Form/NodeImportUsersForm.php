<?php

namespace Drupal\aa_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 
 */
class NodeImportUsersForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The node representing the delivery.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * MediaSettingsForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'node_import_users_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    $this->node = $node;

    $form['users_to_add_csv'] = array(
      '#type' => 'textarea',
      '#required' => TRUE,
      '#title' => $this->t('Usernames (UNICAMENTE la Cédula de identidad para Venezolanos)'),
      '#description' => $this->t('Usernames can be comma separated or number per new line.'),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $users_to_add_csv = $form_state->getValue('users_to_add_csv');
    $users_to_add_csv = str_replace(" ", '', $users_to_add_csv);
    $is_valid_csv = preg_match('/^[0-9]+(,[0-9]+)*$/iD', $users_to_add_csv) || preg_match('/^[0-9]+(\r\n[0-9]+)*$/iD', $users_to_add_csv);

    if (!$is_valid_csv) {
      $form_state->setErrorByName('users_to_add_csv', $this->t('Usernames list is required as a comma separated numeric values or a numeric value per line, eg `1234567,1234568,1234569`'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Users to add.
    $new_users_to_deliver_csv = $form_state->getValue('users_to_add_csv');
    $new_users_to_deliver_csv = str_replace(" ", '', $new_users_to_deliver_csv);
    $new_users_to_deliver_csv = str_replace("\r\n", ',', $new_users_to_deliver_csv);

    $new_users_to_deliver_ids = explode(',', $new_users_to_deliver_csv);
    
    // Remove any duplicates.
    $new_users_to_deliver_ids = array_unique($new_users_to_deliver_ids);
    
    // Load current delivery.
    $users_to_deliver = $this->node->field_users_to_deliver->referencedEntities();
    $users_to_deliver_ids = array_map(function($user) { return $user->getDisplayName() ;}, $users_to_deliver);
    
    // Check if some users are already in the delivery list.
    $users_intersect = array_intersect($new_users_to_deliver_ids, $users_to_deliver_ids);

    if ($users_intersect != null) {
      $new_users_to_deliver_ids = array_diff($new_users_to_deliver_ids, $users_to_deliver_ids);
    }

    if (count($users_intersect) > 0) {
      $this->messenger()->addWarning(
        $this->t(
          'Following usernames have already been added to this delivery and will not be added again: %duplicate_users',
          [
            '%duplicate_users' => implode(', ', $users_intersect),
          ])
        );
    }

    if (
      $new_users_to_deliver_ids != null 
      && count($new_users_to_deliver_ids) > 0
      ) {
      // Load new users.
      $new_users_uid = $this->entityTypeManager->getStorage('user')->getQuery()
        ->condition('name', $new_users_to_deliver_ids, 'IN')
        ->condition('status', TRUE)
        ->execute();
      
      $new_users = $this->entityTypeManager->getStorage('user')->loadMultiple($new_users_uid);
      $new_users_ids = array_map(function($user) { return $user->getDisplayName() ;}, $new_users );
      $excluded_user_ids = array_diff($new_users_to_deliver_ids, $new_users_ids);

      if ($excluded_user_ids != null || count($excluded_user_ids) > 0) {
        // Check if some users do not exist or blocked.
        $this->messenger()->addWarning(
          $this->t(
            "Following usernames have not been added to this delivery as users do not exist in the system or are blocked: %invalid_users",
            [
            '%invalid_users' => implode(', ', $excluded_user_ids)
            ])
          );
      }

      if ($new_users_uid != null && count($new_users_to_deliver_ids) > 0) {
          // Insert new user ids to delivery.
          foreach ($new_users_uid as &$user) {
              $this->node->field_users_to_deliver[] = $user;
          }
          $this->node->save();

          $this->messenger()->addMessage(
            $this->t(
              "Following %count usernames have been assigned to this delivery: %new_users",
              [
                '%count' => count($new_users_ids),
                '%new_users' => implode(', ', $new_users_ids),
              ])
          );
      } else {
        $this->messenger()->addMessage(
          $this->t("No new users have been added")
        );
      }
    }
  }
}
