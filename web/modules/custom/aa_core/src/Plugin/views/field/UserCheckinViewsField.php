<?php

namespace Drupal\aa_core\Plugin\views\field;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("aa_user_checkin_views_field")
 */
class UserCheckinViewsField extends FieldPluginBase {

  /**
   * The current display.
   *
   * @var string
   *   The current display of the view.
   */
  protected $currentDisplay;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The CSRF token generator.
   *
   * @var \Drupal\Core\Access\CsrfTokenGenerator
   */
  protected $tokenGenerator;

  /**
   * Constructs a new BulkForm object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Access\CsrfTokenGenerator $token_generator
   *   The CSRF token generator.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $current_user, CsrfTokenGenerator $token_generator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentUser = $current_user;
    $this->tokenGenerator = $token_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('csrf_token')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->currentDisplay = $view->current_display;
  }

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    // First check whether the field should be hidden if the value(hide_alter_empty = TRUE) /the rewrite is empty (hide_alter_empty = FALSE).
    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $values->_entity;
    $valueCheckIn = $node->get('field_users_check_in')->getValue();
    $valueOptOut = $node->get('field_users_opt_out')->getValue();
    $userCheckIn = array_reduce($valueCheckIn, [$this, 'fieldItemReferenceReduce'], FALSE);
    $userOptOut = array_reduce($valueOptOut, [$this, 'fieldItemReferenceReduce'], FALSE);

    // Generate link original link.
    $link_accept = Link::fromTextAndUrl(
      $this->t('Confirm'),
      Url::fromRoute(
        'aa_core.delivery_confirm',
        ['node' => $node->id()],
        ['query' => ['token' => $this->tokenGenerator->get("user/delivery/{$node->id()}/confirm")]]
      ))->toString();
    $link_reject = Link::fromTextAndUrl(
      $this->t('Reject'),
      Url::fromRoute(
        'aa_core.delivery_reject',
        ['node' => $node->id()],
        ['query' => ['token' => $this->tokenGenerator->get("user/delivery/{$node->id()}/reject")]]
      ))->toString();
    $link = [[
      '#markup' => $link_accept,
    ], [
      '#markup' => ' | ',
    ], [
      '#markup' => $link_reject,
    ]];

    if ($userCheckIn) {
      // Generate link if user already confirmed.
      $link = Link::fromTextAndUrl(
        $this->t('Cancel confirmation and reject'),
        Url::fromRoute(
          'aa_core.delivery_reject',
          ['node' => $node->id()],
          ['query' => ['token' => $this->tokenGenerator->get("user/delivery/{$node->id()}/reject")]]
        ))->toString();
    }

    if ($userOptOut) {
      // Generate link if user already rejected.
      $link = Link::fromTextAndUrl(
        $this->t('Cancel rejection and confirm'),
        Url::fromRoute(
          'aa_core.delivery_confirm',
          ['node' => $node->id()],
          ['query' => ['token' => $this->tokenGenerator->get("user/delivery/{$node->id()}/confirm")]]
        ))->toString();
    }

    return $link;
  }

  /**
   * Constructs a new BulkForm object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function fieldItemReferenceReduce($found, array $item) {
    $found = $found || ($item['target_id'] == $this->currentUser->id());
    return $found;
  }

}
