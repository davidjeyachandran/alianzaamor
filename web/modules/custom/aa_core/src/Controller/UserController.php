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
class UserController extends ControllerBase {

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
   * Redirects user to their particular page.
   *
   * @param string $redirect_path
   *   Path to redirect.
   *
   * @return array
   *   A render array representing the administrative page content.
   */
  public function redirectUser($redirect_path = 'edit'): array {

    $route = [];

    switch ($redirect_path) {
      case 'deliveries':
        // Route for redirecting back.
        $route = Url::fromRoute('view.user_deliveries.page_user_deliveries', [
          'user' => $this->currentUser->id(),
        ]);
        break;

      case 'edit':
      default:
        // Route for redirecting to edit user form.
        $route = Url::fromRoute('entity.user.edit_form', [
          'user' => $this->currentUser->id(),
        ]);
        break;
    }

    $response = new RedirectResponse($route->toString());
    $response->send();

    return [
      '#markup' => 'Processing...',
    ];
  }

}
