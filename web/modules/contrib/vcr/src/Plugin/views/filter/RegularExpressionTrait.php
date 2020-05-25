<?php

namespace Drupal\views_custom_regex\Plugin\views\filter;

trait RegularExpressionTrait {

  protected function createRegularExpression($field, $options, $value) {

    $regex_field = $options['expose']['regex'];
    // Checks if Regular Expression Field is empty
    // if empty then in that case default drupal query of Filter will execute.
    if (!empty($regex_field)) {
      // Depending on position selected Regular expression will be
      // appended in the Query.
      $value = ($options['expose']['position'] == 'prefix') ? $options['expose']['regex']['regex_field'] . $value : $value . $options['expose']['regex']['regex_field'];
      $this->query->addWhereExpression($options['group'], "$field REGEXP '$value'");
    }
    else {
      // If Regular expression field is empty then use default query.
      $this->query->addWhere($options['group'], $field, $value, 'REGEXP');
    }
  }

  protected function opRegexField () {
    $form['regex_field'] = [
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
    return $form;
  }
}
