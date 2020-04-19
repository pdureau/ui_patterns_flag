<?php

namespace Drupal\ui_patterns_flag\Plugin\ActionLink;

use Drupal\flag\Plugin\ActionLink\Reload as OriginalReload;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\ui_patterns\Form\PatternDisplayFormTrait;
use Drupal\ui_patterns_flag\PatternActionLinkTrait;
use Drupal\ui_patterns\UiPatternsSourceManager;
use Drupal\ui_patterns\UiPatternsManager;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\flag\FlagCountManagerInterface;

/**
 * Provides the Normal Link (Reload) link type.
 *
 * @ActionLinkType(
 *   id = "ui_patterns_reload",
 *   label = @Translation("Normal link (with UI Patterns)"),
 *   description = "A normal non-JavaScript request will be made and the current page will be reloaded.")
 */
class Reload extends OriginalReload {

  use PatternDisplayFormTrait, PatternActionLinkTrait {
    PatternActionLinkTrait::getDefaultValue insteadof PatternDisplayFormTrait;
  }

  /**
   * Flag count manager.
   *
   * @var \Drupal\flag\FlagCountManagerInterface
   */
  protected $flagCountManager;

  /**
   * UI Patterns manager.
   *
   * @var \Drupal\ui_patterns\UiPatternsManager
   */
  protected $patternsManager;

  /**
   * UI Patterns source manager.
   *
   * @var \Drupal\ui_patterns\UiPatternsSourceManager
   */
  protected $sourceManager;

  /**
   * A module manager object.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Build a new link type instance and sets the configuration.
   *
   * @param array $configuration
   *   The configuration array with which to initialize this plugin.
   * @param string $plugin_id
   *   The ID with which to initialize this plugin.
   * @param array $plugin_definition
   *   The plugin definition array.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request from the request stack.
   * @param \Drupal\flag\FlagCountManagerInterface $flag_count_manager
   *   Flag count manager.
   * @param \Drupal\ui_patterns\UiPatternsManager $patterns_manager
   *   UI Patterns manager.
   * @param \Drupal\ui_patterns\UiPatternsSourceManager $source_manager
   *   UI Patterns source manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   Module handler.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, AccountInterface $current_user, Request $request, FlagCountManagerInterface $flag_count_manager, UiPatternsManager $patterns_manager, UiPatternsSourceManager $source_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $current_user, $request);
    $this->flagCountManager = $flag_count_manager;
    $this->patternsManager = $patterns_manager;
    $this->sourceManager = $source_manager;
    $this->moduleHandler = $module_handler;
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
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('flag.count'),
      $container->get('plugin.manager.ui_patterns'),
      $container->get('plugin.manager.ui_patterns_source'),
      $container->get('module_handler')
    );
  }

}
