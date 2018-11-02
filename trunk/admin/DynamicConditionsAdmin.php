<?php namespace Admin;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    DynamicConditions
 * @subpackage DynamicConditions/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    DynamicConditions
 * @subpackage DynamicConditions/admin
 * @author     RTO GmbH <kundenhomepage@rto.de>
 */
class DynamicConditionsAdmin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $pluginName    The ID of this plugin.
	 */
	private $pluginName;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $pluginName       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $pluginName, $version ) {

		$this->pluginName = $pluginName;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueueStyles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in DynamicConditionsLoader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The DynamicConditionsLoader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->pluginName, plugin_dir_url( __FILE__ ) . 'css/dynamic-conditions-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueueScripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in DynamicConditionsLoader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The DynamicConditionsLoader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->pluginName, plugin_dir_url( __FILE__ ) . 'js/dynamic-conditions-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Creates section for dynamic conditions in elementor-widgets
     *
     * @param $element
     * @param $section_id
     * @param $args
     */
    public function addConditionFields( $element, $section_id, $args ) {
        if ( false && !\Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return;
        }
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
            'dynamiccondtions_dynamic',
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
                'placeholder' => __( 'Select you dynamic condition', 'dynamic-condtions' ),
            ]
        );



        $element->add_control(
            'dynamicconditions_visibility',
            [
                'label' => __( 'Show/Hide', 'dynamic-conditions' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'hide',
                'options' => [
                    'show' => __( 'Show when Condition\'s Met' ),
                    'hide' => __( 'Hide when Condition\'s Met' ),
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
                    'equal' => __( 'Is Equal To', 'dynamic-conditions' ),
                    'not_equal' => __( 'Is Not Equal To', 'dynamic-conditions' ),
                    'contains' => __( 'Contains', 'dynamic-conditions' ),
                    'not_contains' => __( 'Does Not Contain', 'dynamic-conditions' ),
                    'empty' => __( 'Is Empty', 'dynamic-conditions' ),
                    'not_empty' => __( 'Is Not Empty', 'dynamic-conditions' ),
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
                'label' => __( 'Conditional Value', 'dynamic-conditions' ),
                'description' => __( 'Add your Conditional Value here if you selected Equal to, Not Equal To or Contains on the selection above.', 'dynamic-conditions' ),
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
