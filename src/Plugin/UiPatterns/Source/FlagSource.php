<?php

namespace Drupal\ui_patterns_flag\Plugin\UiPatterns\Source;

use Drupal\ui_patterns\Plugin\PatternSourceBase;

/**
 * Defines Field values source plugin.
 *
 * @UiPatternsSource(
 *   id = "flag",
 *   label = @Translation("Flag"),
 *   tags = {
 *     "flag"
 *   }
 * )
 */
class FlagSource extends PatternSourceBase {

  /**
   * {@inheritdoc}
   */
  public function getSourceFields() {
    $sources = [];
    $sources[] = $this->getSourceField('action', 'Action (flag or unflag)');
    $sources[] = $this->getSourceField('title', 'Title');
    $sources[] = $this->getSourceField('url', 'URL');
    $sources[] = $this->getSourceField('count', 'Flag count');
    return $sources;
  }

}
