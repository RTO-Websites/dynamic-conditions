<?php namespace Lib;

use Admin\DynamicConditionsAdmin;
use Pub\DynamicConditionsPublic;

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

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      DynamicConditionsLoader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $pluginName The string used to uniquely identify this plugin.
     */
    protected $pluginName;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

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
        $this->version = '1.0.0';

        $this->loadDependencies();
        $this->setLocale();
        $this->defineAdminHooks();
        $this->definePublicHooks();

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
    private function loadDependencies() {

        $this->loader = new DynamicConditionsLoader();

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
    private function setLocale() {

        $pluginI18n = new DynamicConditionsI18n();
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
    private function defineAdminHooks() {
        $pluginAdmin = new DynamicConditionsAdmin( $this->getDynamicConditions(), $this->getVersion() );

        $this->loader->addAction( 'elementor/element/section/section_advanced/after_section_end', $pluginAdmin, 'addConditionFields', 10, 3 );
        $this->loader->addAction( 'elementor/element/common/_section_style/after_section_end', $pluginAdmin, 'addConditionFields', 10, 3 );

        $this->loader->addAction( 'admin_notices', $pluginAdmin, 'addAdminNotices', 10, 3 );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function definePublicHooks() {

        $pluginPublic = new DynamicConditionsPublic( $this->getDynamicConditions(), $this->getVersion() );

        // filter widgets
        $this->loader->addAction( 'elementor/widget/render_content', $pluginPublic, 'filterWidgetContent', 10, 2 );

        // filter sections
        $this->loader->addAction( "elementor/frontend/section/before_render", $pluginPublic, 'filterSectionContentBefore', 10, 1 );
        $this->loader->addAction( "elementor/frontend/section/after_render", $pluginPublic, 'filterSectionContentAfter', 10, 1 );
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getDynamicConditions() {
        return $this->pluginName;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    DynamicConditionsLoader    Orchestrates the hooks of the plugin.
     */
    public function getLoader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public static function run() {
        $plugin = new self();
        $plugin->loader->run();
    }
}
