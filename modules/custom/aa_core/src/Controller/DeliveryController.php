<?php

namespace Drupal\aa_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Messenger\MessengerInterface;
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
   * Page for executing deliverying
   *
   * Receive the delivery node id and user id. The delivery node will be
   * updated with a new user value for field "field_delivered".
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to export.
   *
   * @param \Drupal\user\UserInterface $user
   *   The account for which a personal contact form should be generated.
   *
   * @return array
   *   A render array representing the administrative page content.
   */
  public function toDeliver(NodeInterface $node, UserInterface $user) : array {

    // @TODO: Improve below logic.
    // Move to access node class.
    if ($node->bundle() !== 'delivery') {
      throw new NotFoundHttpException;
    }
    if (!$user->isAuthenticated()) {
      throw new NotFoundHttpException();
    }
    // Route for redirecting back.
    $route = Url::fromRoute('view.miembros.page_users_to_deliver', [
      'arg_0' => $node->id(),
    ]);
    $response = new RedirectResponse($route->toString());

    $users = $node->field_delivered->referencedEntities();
    $users_found = array_filter($users, static function (UserInterface $user_reference) use ($user) {
      return $user_reference->id() === $user->id();
    });
    if (!empty($users_found)) {
      $message = t('A member with cedula/dni %id is duplicated', [
        '%id' => $user->getAccountName(),
      ]);
      \Drupal::messenger()->addMessage($message, MessengerInterface::TYPE_WARNING);
      $response->send();
      return [];
    }
    // Adding a user if delivered food.
    try {
      $node->field_delivered[] = $user->id();
      $node->save();

      // In Spanish so far since we are doing for spanish.
      $user->field_delivery_date->value = $node->field_time->value;
      $user->save();

      $message = t('A member with cedula/dni %id has been delivered food', [
        '%id' => $user->getAccountName(),
      ]);
      \Drupal::messenger()->addMessage($message);
    }
    catch (EntityStorageException $exception) {
      $message = t('A member with cedula/dni %id COULD NOT been delivered food', [
        '%id' => $user->getAccountName(),
      ]);
      \Drupal::messenger()->addMessage($message, MessengerInterface::TYPE_ERROR);
    }

    $response->send();

    return [
      '#markup' => 'Delivering...',
    ];
  }

}
