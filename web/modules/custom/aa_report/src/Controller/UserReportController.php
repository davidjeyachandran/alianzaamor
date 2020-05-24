<?php

namespace Drupal\aa_report\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class UserReportController.
 *
 * @package Drupal\aa_report\Controller
 */
class UserReportController extends ControllerBase {

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
    $query->fields('u', [$field]);
    // $query->range(0, 50);
    $num_rows = $query->countQuery()->execute()->fetchField();
    $result = $query->execute();
    $records = $result->fetchAllAssoc($field);
    $phones = [];
    foreach (array_keys($records) as $record) {
      $phones[] = $record;
    }

    $build = [
      '#markup' => $this->t('Phone records not matching 9xxxxxxxx: @num<br />@nums', [
        '@num' => $num_rows,
        '@nums' => implode("<br>", $phones),
      ]),
    ];

    return $build;
  }

}
