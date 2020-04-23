<?php

namespace Drupal\ui_patterns_flag;

use Drupal\Core\Entity\EntityInterface;
use Drupal\flag\FlagInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Methods shared between ActionLink plugins.
 *
 * See also: Drupal\ui_patterns\Form\PatternDisplayFormTrait.
 */
trait PatternActionLinkTrait {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'pattern' => '_none',
      'variants' => '',
      'pattern_mapping' => [],
      // Used by ui_patterns_settings.
      'pattern_settings' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    $form['#tree'] = TRUE;
    // Add UI Patterns form elements.
    $context = [];
    $pattern = $config['pattern'];
    if ($pattern_variant = $this->getCurrentVariant($pattern)) {
      $config['pattern_variant'] = $pattern_variant;
    }
    $this->buildPatternDisplayForm($form, 'flag', $context, $config);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getAsFlagLink(FlagInterface $flag, EntityInterface $entity) {
    $build = parent::getAsFlagLink($flag, $entity);
    $config = $this->getConfiguration();
    $fields = [];
    $pattern = $config['pattern'];
    if (!$pattern || $pattern === '_none') {
      return $build;
    }
    // "Display link as field" has to be checked.
    if (!array_key_exists('#title', $build)) {
      return $build;
    }

    // Gather all values of each fields.
    $mapping = $config['pattern_mapping'];
    $mapping = $mapping[$pattern]['settings'];
    foreach ($mapping as $source => $field) {
      if ($field['destination'] === '_hidden') {
        continue;
      }
      if ($source == 'flag:action') {
        $fields[$field['destination']][] = $build['#action'];
      }
      if ($source == 'flag:title') {
        $fields[$field['destination']][] = $build['#title']['#markup'];
      }
      if ($source == 'flag:url') {
        $fields[$field['destination']][] = $build['#attributes']['href'];
      }
      if ($source == 'flag:count') {
        $counter = $this->flagCountManager->getEntityFlagCounts($entity);
        $count = 0;
        if (array_key_exists($flag->id(), $counter)) {
          $count = $counter[$flag->id()];
        }
        $fields[$field['destination']][] = $count;
      }
    }

    // Concat all values of each field.
    foreach ($fields as $field => $values) {
      $fields[$field] = implode(' ', $values);
    }

    // Build the pattern element.
    $element = [
      '#type' => 'pattern',
      '#id' => $config['pattern'],
      '#fields' => $fields,
    ];
    // Set the variant.
    if ($pattern_variant = $this->getCurrentVariant($pattern)) {
      $element['#variant'] = $pattern_variant;
    }
    // Set the settings.
    $settings = $config['pattern_settings'];
    $pattern_settings = !empty($settings) && isset($settings[$pattern]) ? $settings[$pattern] : NULL;
    if (isset($pattern_settings)) {
      $element['#settings'] = $pattern_settings;
    }

    $build['#title'] = $element;
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultValue(array $configuration, $field_name, $value) {
    // Some modifications to make 'destination' default value working.
    $pattern = $configuration['pattern'];
    if (isset($configuration['pattern_mapping'][$pattern]['settings'][$field_name][$value])) {
      return $configuration['pattern_mapping'][$pattern]['settings'][$field_name][$value];
    }
    return NULL;
  }

  /**
   * Checks if a given pattern has a corresponding value on the variants array.
   *
   * @param string $pattern
   *   Pattern ID.
   *
   * @return string|null
   *   Variant ID.
   */
  protected function getCurrentVariant($pattern) {
    $variants = $this->getConfiguration()['variants'];
    return !empty($variants) && isset($variants[$pattern]) ? $variants[$pattern] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration = $form_state->getValues();
  }

}
