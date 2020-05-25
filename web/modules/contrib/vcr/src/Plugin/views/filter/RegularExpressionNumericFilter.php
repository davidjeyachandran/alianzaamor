<?php

namespace Drupal\views_custom_regex\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\NumericFilter;
use Drupal\Core\Form\FormStateInterface;

/**
 * Simple filter to handle greater than/less than filters.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("numeric")
 */
class RegularExpressionNumericFilter extends NumericFilter {

  /**
   * Overrides defineOptions function.
   *
   * Drupal\views\Plugin\views\filter\NumericFilter.
   *
   * Information about new options added for Regular expression.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['expose']['contains']['regex'] = ['default' => ''];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultExposeOptions() {
    parent::defaultExposeOptions();
    $this->options['expose']['regex'] = '';
  }

  /**
   * Overrides buildExposeForm function.
   *
   * Drupal\views\Plugin\views\filter\StringFilter.
   *
   * New fields are added to expose form.
   */
  public function buildExposeForm(&$form, FormStateInterface $form_state) {
    parent::buildExposeForm($form, $form_state);

    $form['expose']['regex'] = [
      '#type' => 'textfield',
      '#default_value' => $this->options['expose']['regex'],
      '#title' => $this->t('Regular Expression'),
      '#description' => $this->t('Enter a regular expression. Example: [^abc] The expression is used to find any character NOT between the brackets'),
      '#size' => 20,
      '#states' => [
        'visible' => [
          'select[name="options[operator]"]' => ['value' => 'regular_expression'],
        ],
      ],
    ];
  }

  /**
   * Filters by a regular expression.
   *
   * @param string $field
   *   The expression pointing to the queries field, for example "foo.bar".
   */
  protected function opRegex($field) {
    $regex_field = $this->options['expose']['regex'];
    // Checks if Regular Expression Field is empty
    // if empty then in that case default drupal query of Filter will execute.
    if (!empty($regex_field)) {
      // Depending on position selected Regular expression,
      // will be appended in the Query.
      $this->query->addWhereExpression($this->options['group'], "$field REGEXP ' $regex_field$this->value'");
    }
    else {
      // If Regular expression field is empty then use default query.
      $this->query->addWhere($this->options['group'], $field, $this->value, 'REGEXP');
    }
  }

}
