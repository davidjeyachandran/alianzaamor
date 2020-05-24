<?php

namespace Drupal\aa_report\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Users/Deliveries Total' block.
 *
 * @Block(
 *   id = "aa_report_user_deliveries_total_block",
 *   admin_label = @Translation("AA Report: Users/Deliveries total")
 * )
 */
class UserDeliveriesTotalBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new AaUserBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['label_display' => FALSE];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get total user count.
    $query = $this->entityTypeManager->getStorage('user')->getAggregateQuery('AND')
    ->condition('status', 1)
    ->condition('roles', NULL, 'IS NULL')
    ->aggregate('uid', 'COUNT');
    $result = $query->execute();
    $total_users = $result[0]['uid_count'];

    // Get total deliveries count.
    $database = \Drupal::database();
    $query = $database->select('node__field_delivered', 'u');
    $total_deliveries = $query->countQuery()->execute()->fetchField();
  
    $renderable = [
      '#theme' => 'aa_report_user_deliveries_total_block',
      '#total_users' => $total_users,
      '#total_deliveries' => $total_deliveries,
    ];

    return $renderable;
  }

  public function getCacheMaxAge() {
    return 0;
  }
}
