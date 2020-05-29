<?php

namespace Drupal\aa_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller routines for delivery controller.
 */
class DeliveryController extends ControllerBase {

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
  public function updateDelivery(NodeInterface $node, UserInterface $user, $field_name = 'field_delivered') : array {

    // @TODO: Improve below logic.
    // Move to access node class.
    if ($node->bundle() !== 'delivery') {
      throw new NotFoundHttpException;
    }

    if (!$user->isAuthenticated()) {
      throw new NotFoundHttpException();
    }

    // Check that correct field is used.
    if (in_array($field_name, ['field_delivered', 'field_users_check_in', 'field_users_opt_out'])) {
      throw new NotFoundHttpException;
    }

    // Route for redirecting back.
    $route = Url::fromRoute('view.miembros.page_users_to_deliver', [
      'node' => $node->id(),
    ]);
    $response = new RedirectResponse($route->toString());

    $users = $node->$field_name->referencedEntities();
    $users_found = array_filter($users, static function (UserInterface $user_reference) use ($user) {
      return $user_reference->id() === $user->id();
    });
    if (!empty($users_found)) {
      $message = $this->t('A member with cedula/dni %id is duplicated', [
        '%id' => $user->getAccountName(),
      ]);
      $this->messenger()->addWarning($message);
    }
    else {

      // Adding a user if delivered food.
      try {
        $node->$field_name[] = $user->id();
        $node->save();

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
    }

    $response->send();
    return [
      '#markup' => 'Processing...',
    ];
  }

}
