<?php

namespace Drupal\aa_core\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Controller routines for delivery controller.
 */
class DeliveryController extends ControllerBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * SystemBrandingOffCanvasForm constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Configuration factory.
   */
  public function __construct(AccountInterface $current_user, ConfigFactoryInterface $config_factory) {
    $this->currentUser = $current_user;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('config.factory')
    );
  }

  /**
   * Page for executing delivering
   *
   * Receive the delivery node id and user id. The delivery node will be
   * updated with a new user value for field "field_delivered".
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to export.
   * @param \Drupal\user\UserInterface $user
   *   The account for which a personal contact form should be generated.
   * @param string $field_name
   *   Field name to update: field_delivered, field_users_check_in or field_users_opt_out.
   *
   * @return array
   *   A render array representing the administrative page content.
   */
  public function updateDelivery(NodeInterface $node, UserInterface $user = NULL, $field_name = 'field_delivered'): array {
    $config = $this->configFactory->get('aa_core.settings');

    // @TODO: Improve below logic.
    // Move to access node class.
    if ($node->bundle() !== 'delivery') {
      throw new AccessDeniedHttpException("Content type is not supported.");
    }

    if (!$user) {
      $user = $this->currentUser;
    }

    if (!$user->isAuthenticated()) {
      throw new AccessDeniedHttpException("User doesn't have permissions.");
    }

    // Check that correct field is used.
    if (!in_array($field_name, ['field_delivered', 'field_users_check_in', 'field_users_opt_out'])) {
      throw new AccessDeniedHttpException("Field doesn't exists.");
    }

    $users = $node->$field_name->referencedEntities();
    $users_found = array_filter($users, static function (UserInterface $user_reference) use ($user) {
      return $user_reference->id() === $user->id();
    });

    // Define default route.
    $route = Url::fromRoute('view.aa_user_deliveries.page_user_deliveries', [
      'user' => $user->id(),
    ]);

    if (!empty($users_found)) {
      // When user was added previously to the field.
      $message = $this->t('A member with cedula/dni %id is duplicated', [
        '%id' => $user->getAccountName(),
      ]);
      $this->messenger()->addWarning($message);
    }
    else {

      switch ($field_name) {
        case 'field_delivered':
          // Update user when food was delivered.
          try {
            // In Spanish so far since we are doing for spanish.
            $user->field_delivery_date->value = $node->field_time->value;
            $user->save();

            $message = t('A member with cedula/dni %id has been delivered food', [
              '%id' => $user->getAccountName(),
            ]);
            $this->messenger()->addMessage($message);
          } catch (EntityStorageException $exception) {
            $message = t('A member with cedula/dni %id COULD NOT been delivered food', [
              '%id' => $user->getAccountName(),
            ]);
            $this->messenger()->addError($message);
          }

          // Route for redirecting back.
          $route = Url::fromRoute('view.miembros.page_users_to_deliver', [
            'node' => $node->id(),
          ]);

          break;

        case 'field_users_check_in':
          // Exclude value from the other field.
          $users_check = $node->get('field_users_opt_out')->getValue();
          $new_value = [];
          foreach ($users_check as $user_check) {
            if ($user_check['target_id'] != $user->id()) {
              $new_value[] = ['target_id' => $user_check['target_id']];
            }
          }
          $node->set('field_users_opt_out', $new_value);

          // Update user when confirmation response received.
          $message = $this->t($config->get('delivery.confirmed_message'));
          $this->messenger()->addMessage($message);
          break;

        case 'field_users_opt_out':
          // Exclude value from the other field.
          $users_check = $node->get('field_users_check_in')->getValue();
          $new_value = [];
          foreach ($users_check as $user_check) {
            if ($user_check['target_id'] != $user->id()) {
              $new_value[] = ['target_id' => $user_check['target_id']];
            }
          }
          $node->set('field_users_check_in', $new_value);

          // Update user when rejection response received.
          $message = $this->t($config->get('delivery.rejected_message'));
          $this->messenger()->addMessage($message);
          break;
      }

      // Updating field.
      $node->$field_name[] = $user->id();
      $node->save();

    }

    $response = new RedirectResponse($route->toString());
    $response->send();

    return [
      '#markup' => 'Processing...',
    ];
  }

}
