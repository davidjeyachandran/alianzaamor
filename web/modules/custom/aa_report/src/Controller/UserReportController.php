<?php

namespace Drupal\aa_report\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\KeyValueStore\KeyValueExpirableFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserReportController.
 *
 * @package Drupal\aa_report\Controller
 */
class UserReportController implements ContainerInjectionInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a UserReportController object.
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
   * Returns a render-able array for a test page.
   */
  public function content() {
    // Create an object of type Select.
    $database = \Drupal::database();
    $query = $database->select('user__field_celular', 'u');

    // Add extra detail to this query object: a condition, fields and a range.
    $field = 'field_celular_value';
    $query->condition('u.' . $field, '^5[1|6][0-9]{8}', 'NOT REGEXP');
    $query->fields('u', ['entity_id', $field]);
    // $query->range(0, 50);
    $num_rows = $query->countQuery()->execute()->fetchField();
    $result = $query->execute();
    $records = $result->fetchAllKeyed();
    dump($records);

    $phones = [];
    foreach ($records as $index => $record) {

      $phones[] = [
        $record['entity_id'],
        $record['field_celular_value'],
      ];
    }

    $build = [
      '#markup' => $this->t('Phone records not matching 9xxxxxxxx: @num<br />@nums', [
        '@num' => $num_rows,
        '@nums' => implode("<br>", $phones),
      ]),
    ];

    $this->entityTypeManager->getStorage('user')->getAggregateQuery('AND')
      ->condition('roles', NULL, 'IS NULL')
      ->aggregate('field_celular', '^5[1|6][0-9]{8}', 'NOT REGEXP');
    $result = $query->execute();
    //$total_users = $result[0]['uid_count'];
    dump($result);

    $header = [
      ['data' => $this->t('Phone'), 'field' => 'phone', 'sort' => 'desc'],
      ['data' => $this->t('User'), 'field' => 'user'],
    ];
    return [
      '#type' => 'table',
      '#header' => $header,
      '#title' => $this->t('Phones'),
      '#empty' => $this->t('No phone anomalies found yet.'),
      '#rows' => $phones,
      '#attributes' => [
        'class' => ['phone-incorrect-table'],
      ],
    ];
  }

}
