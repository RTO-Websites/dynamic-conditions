<?php namespace Lib;

use Admin\DynamicConditionsAdmin;
use Pub\DynamicConditionsPublic;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

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

    private $widgetsWithCondition = [];

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      DynamicConditionsLoader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $pluginName    The string used to uniquely identify this plugin.
	 */
	protected $pluginName;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
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
		$pluginI18n->setDomain( $this->getDynamicConditions() );

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

		$this->loader->addAction( 'admin_enqueue_scripts', $pluginAdmin, 'enqueueStyles' );
		$this->loader->addAction( 'admin_enqueue_scripts', $pluginAdmin, 'enqueueScripts' );

        add_action( 'elementor/element/after_section_end', [$this, 'addConditionFields'], 10, 3 );
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

		$this->loader->addAction( 'wp_enqueue_scripts', $pluginPublic, 'enqueueStyles' );
        $this->loader->addAction( 'wp_enqueue_scripts', $pluginPublic, 'enqueueScripts' );

        add_action( 'elementor/element/after_section_end', [$this, 'addConditionFields'], 10, 3 );
        add_action( 'elementor/widget/render_content', [$pluginPublic, 'hookRenderContent'], 10, 3 );
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

    /**
     * Creates section for dynamic conditions in elementor-widgets
     *
     * @param $element
     * @param $section_id
     * @param $args
     */
    public function addConditionFields( $element, $section_id, $args ) {
        /* if ( false && !\Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return;
        } */
        //filter the elements first to avoid conflicts that can cause pagebuilder not to load
        if ( in_array( $element->get_name(), array( 'global-settings', 'section', 'page-settings', 'oew-blog-grid' ) ) ) {
            return;
        }

        $whitelist = array(
            'section_image',
            'section_advanced',
            'section_title',
            'section_editor',
            'section_video',
            'section_button',
            'section_divider',
            'section_spacer',
            'section_map',
            'section_icon',
            'section_gallery',
            'section_image_carousel',
            'section_icon_list',
            'section_counter',
            'section_testimonial',
            'section_tabs',
            'section_toggle',
            'section_social_icon',
            'section_alert',
            'section_audio',
            'section_shortcode',
            'section_anchor',
            'section_sidebar',
            'section_layout',
            'section_slides',
            'section_form_fields',
            'section_list',
            'section_header',
            'section_pricing',
            'section_countdown',
            'section_buttons_content',
            'section_blockquote_content',
            'section_content',
            'section_login_content',
            'text_elements',
            'section_side_a_content',
            'section_side_b_content',
            '_section_style',
        );


        if ( !in_array( $section_id, $whitelist ) ) {
            return;
        }

        if ( in_array($element->get_name(), $this->widgetsWithCondition)) {
            // dont add multiple, to prevent notices
            return;
        }
        $this->widgetsWithCondition[] = $element->get_name();

        $element->start_controls_section(
            'dynamicconditions_section',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => __( 'Dynamic Conditions', 'dynamic-conditions' ),
            ],
            [
                'overwrite' => true,
            ]
        );
        /*$element->start_controls_tabs( 'dynamic_conditions_tabs',[
            'overwrite'         => true
        ] );

        $element->start_controls_tab( 'dynamic_conditions_tab',
            [
                'label' => __( 'Dynamic Condition', 'dynamic-conditions' )
            ],
            [
            'overwrite'         => true
        ] );*/

        $element->add_control(
            'dynamicconditions_dynamic',
            [
                'label' => __( 'Dynamic Tag', 'dynamic-condtions' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        Module::TEXT_CATEGORY,
                        Module::URL_CATEGORY,
                        Module::GALLERY_CATEGORY,
                        Module::IMAGE_CATEGORY,
                        Module::MEDIA_CATEGORY,
                        Module::POST_META_CATEGORY
                    ],
                ],
                'returnType' => 'array',
                'placeholder' => __( 'Select condition field', 'dynamic-condtions' ),
            ]
        );



        $element->add_control(
            'dynamicconditions_visibility',
            [
                'label' => __( 'Show/Hide', 'dynamic-conditions' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'hide',
                'options' => [
                    'show' => __( 'Show when condition met', 'dynamic-conditions' ),
                    'hide' => __( 'Hide when condition met', 'dynamic-conditions' ),
                ],
                'separator' => 'before',
            ],
            [
                'overwrite' => true,
            ]
        );


        $element->add_control(
            'dynamicconditions_condition',
            [
                'label' => __( 'Condition', 'dynamic-conditions' ),
                'type' => Controls_Manager::SELECT2,
                'multiple' => false,
                'label_block' => true,
                'options' => [
                    'equal' => __( 'Is equal to', 'dynamic-conditions' ),
                    'not_equal' => __( 'Is not equal to', 'dynamic-conditions' ),
                    'contains' => __( 'Contains', 'dynamic-conditions' ),
                    'not_contains' => __( 'Does not contain', 'dynamic-conditions' ),
                    'empty' => __( 'Is empty', 'dynamic-conditions' ),
                    'not_empty' => __( 'Is not empty', 'dynamic-conditions' ),
                ],
                'render_type' => 'none',
                'description' => __( 'Select your condition for this widget visibility.', 'dynamic-conditions' ),
            ],
            [
                'overwrite' => true,
            ]
        );
        $element->add_control(
            'dynamicconditions_value',
            [
                'type' => Controls_Manager::TEXTAREA,
                'label' => __( 'Conditional value', 'dynamic-conditions' ),
                'description' => __( 'Add your conditional value here if you selected equal to, not equal to or contains on the selection above.', 'dynamic-conditions' ),
                // 'separator'     => 'none',
            ],
            [
                'overwrite' => true,
            ]
        );

        //$element->end_controls_tab();
        //$element->end_controls_tabs();
        $element->end_controls_section();
    }
}
