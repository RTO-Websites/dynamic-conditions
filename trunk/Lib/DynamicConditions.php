<?php

namespace DynamicConditions\Lib;

use DynamicConditions\Admin\DynamicConditionsAdmin;
use Elementor\Core\DynamicTags\Manager;
use Elementor\Plugin;
use DynamicConditions\Lib\DynamicTags\NumberPostsTag;
use DynamicConditions\Pub\DynamicConditionsPublic;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    DynamicConditions
 * @subpackage DynamicConditions/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    DynamicConditions
 * @subpackage DynamicConditions/includes
 * @author     RTO GmbH <kundenhomepage@rto.de>
 */
class DynamicConditions {

    protected Loader $loader;

    protected string $pluginName;

    protected string $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->pluginName = 'dynamic-conditions';
        $this->version = DynamicConditions_VERSION;

        $this->loadDependencies();
        $this->setLocale();

        $this->defineAdminHooks();
        $this->definePublicHooks();
        $this->defineElementorHooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - DynamicConditionsLoader. Orchestrates the hooks of the plugin.
     * - DynamicConditionsI18n. Defines internationalization functionality.
     * - DynamicConditionsAdmin. Defines all hooks for the admin area.
     * - DynamicConditionsPublic. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function loadDependencies(): void {

        $this->loader = new Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the DynamicConditionsI18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function setLocale(): void {

        $pluginI18n = new I18n();
        $pluginI18n->setDomain( 'dynamicconditions' );

        $this->loader->addAction( 'plugins_loaded', $pluginI18n, 'loadPluginTextdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineAdminHooks(): void {
        $pluginAdmin = new DynamicConditionsAdmin( $this->getDynamicConditions(), $this->getVersion() );

        $this->loader->addAction( 'elementor/element/column/section_advanced/after_section_end', $pluginAdmin, 'addConditionFields', 10, 3 );
        $this->loader->addAction( 'elementor/element/section/section_advanced/after_section_end', $pluginAdmin, 'addConditionFields', 10, 3 );
        $this->loader->addAction( 'elementor/element/common/_section_style/after_section_end', $pluginAdmin, 'addConditionFields', 10, 3 );

        $this->loader->addAction( 'elementor/element/popup/section_advanced/after_section_end', $pluginAdmin, 'addConditionFields', 10, 3 );

        $this->loader->addAction( 'elementor/element/container/section_layout/after_section_end', $pluginAdmin, 'addConditionFields', 10, 3 );

        $this->loader->addAction( 'admin_notices', $pluginAdmin, 'addAdminNotices', 10, 3 );
        $this->loader->addAction( 'admin_enqueue_scripts', $pluginAdmin, 'enqueueStyles' );
        $this->loader->addAction( 'elementor/editor/before_enqueue_styles', $pluginAdmin, 'enqueueStyles' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function definePublicHooks(): void {
        $pluginPublic = new DynamicConditionsPublic( $this->getDynamicConditions(), $this->getVersion() );

        $this->loader->addAction( 'wp_enqueue_scripts', $pluginPublic, 'enqueueScripts' );

        // filter widgets
        $this->loader->addAction( "elementor/frontend/widget/before_render", $pluginPublic, 'filterSectionContentBefore', 10, 1 );
        $this->loader->addAction( "elementor/frontend/widget/after_render", $pluginPublic, 'filterSectionContentAfter', 10, 1 );

        // filter sections
        $this->loader->addAction( "elementor/frontend/section/before_render", $pluginPublic, 'filterSectionContentBefore', 10, 1 );
        $this->loader->addAction( "elementor/frontend/section/after_render", $pluginPublic, 'filterSectionContentAfter', 10, 1 );

        // filter columns
        $this->loader->addAction( "elementor/frontend/column/before_render", $pluginPublic, 'filterSectionContentBefore', 10, 1 );
        $this->loader->addAction( "elementor/frontend/column/after_render", $pluginPublic, 'filterSectionContentAfter', 10, 1 );

        // filter container
        $this->loader->addAction( "elementor/frontend/container/before_render", $pluginPublic, 'filterSectionContentBefore', 10, 1 );
        $this->loader->addAction( "elementor/frontend/container/after_render", $pluginPublic, 'filterSectionContentAfter', 10, 1 );

        // filter popup
        $this->loader->addAction( "elementor/theme/before_do_popup", $pluginPublic, 'checkPopupsCondition', 10, 1 );
    }

    /**
     * Register all of the hooks related to the elementor-facing functionality
     * of the plugin.
     *
     * @since    1.2.0
     * @access   private
     */
    private function defineElementorHooks(): void {
        $this->loader->addAction( 'elementor/dynamic_tags/register', $this, 'registerDynamicTags', 10, 1 );
        $this->loader->addAction( 'wp_footer', $this, 'setFooterStyleForPreview', 10, 0 );
    }

    public function registerDynamicTags( Manager $dynamicTags ): void {
        $dynamicTags->register( new NumberPostsTag );
    }

    /**
     * Sets style for preview
     *
     * @since 1.3.0
     */
    public function setFooterStyleForPreview(): void {
        if ( !class_exists('Elementor\Plugin') || !Plugin::$instance->preview->is_preview_mode() ) {
            return;
        }
        ?>
        <style>
            body.elementor-editor-active .elementor-element.dc-has-condition::after {
                content: '\e8ed';
                display: inline-block;
                position: absolute;
                top: 0;
                right: 5px;
                font-size: 15px;
                font-family: eicons;
                color: #71d7f7;
            }
            body.elementor-editor-active .elementor-element.dc-has-condition {
              border: 1px dashed rgba(255, 0,0, 0.3);
            }
        </style>
        <?php
    }

    public function getDynamicConditions(): string {
        return $this->pluginName;
    }

    public function getLoader(): Loader {
        return $this->loader;
    }

    public function getVersion(): string {
        return $this->version;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public static function run(): void {
        $plugin = new self();
        $plugin->loader->run();
    }
}