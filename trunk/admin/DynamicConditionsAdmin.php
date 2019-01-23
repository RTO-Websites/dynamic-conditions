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
     * @var      string $pluginName The ID of this plugin.
     */
    private $pluginName;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $pluginName The name of this plugin.
     * @param      string $version The version of this plugin.
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

        wp_enqueue_style( $this->pluginName, plugin_dir_url( __FILE__ ) . 'css/dynamic-conditions-admin.css', [], $this->version, 'all' );

    }

    public function addAdminNotices() {
        $message = '';
        $class = 'notice notice-error';

        if ( !defined( 'ELEMENTOR_VERSION' ) && !defined( 'ELEMENTOR_PRO_VERSION' ) ) {
            $message = __( 'Elementor and Elementor Pro not installed.', 'dynamic-conditions' );
        } else if ( !defined( 'ELEMENTOR_PRO_VERSION' ) ) {
            $message = __( 'Elementor Pro not installed.', 'dynamic-conditions' );
        } else if ( !defined( 'ELEMENTOR_VERSION' ) ) {
            $message = __( 'Elementor not installed.', 'dynamic-conditions' );
        }


        if ( empty( $message ) ) {
            return;
        }
        printf( '<div class="%1$s"><p>DynamicConditions: %2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }


    /**
     * Creates section for dynamic conditions in elementor-widgets
     *
     * @param $element
     * @param $section_id
     * @param $args
     */
    public function addConditionFields( $element, $section_id = null, $args = null ) {
        $element->start_controls_section(
            'dynamicconditions_section',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => __( 'Dynamic Conditions', 'dynamicconditions' ),
            ],
            [
                'overwrite' => true,
            ]
        );

        $element->add_control(
            'dynamicconditions_dynamic',
            [
                'label' => __( 'Dynamic Tag', 'dynamiccondtions' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        Module::TEXT_CATEGORY,
                        Module::URL_CATEGORY,
                        Module::GALLERY_CATEGORY,
                        Module::IMAGE_CATEGORY,
                        Module::MEDIA_CATEGORY,
                        Module::POST_META_CATEGORY,
                    ],
                ],
                'returnType' => 'array',
                'placeholder' => __( 'Select condition field', 'dynamiccondtions' ),
            ]
        );


        $element->add_control(
            'dynamicconditions_visibility',
            [
                'label' => __( 'Show/Hide', 'dynamic-conditions' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'hide',
                'options' => [
                    'show' => __( 'Show when condition met', 'dynamicconditions' ),
                    'hide' => __( 'Hide when condition met', 'dynamicconditions' ),
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
                'label' => __( 'Condition', 'dynamicconditions' ),
                'type' => Controls_Manager::SELECT2,
                'multiple' => false,
                'label_block' => true,
                'options' => [
                    'equal' => __( 'Is equal to', 'dynamicconditions' ),
                    'not_equal' => __( 'Is not equal to', 'dynamicconditions' ),
                    'contains' => __( 'Contains', 'dynamicconditions' ),
                    'not_contains' => __( 'Does not contain', 'dynamicconditions' ),
                    'empty' => __( 'Is empty', 'dynamicconditions' ),
                    'not_empty' => __( 'Is not empty', 'dynamicconditions' ),
                    'less' => __( 'Less than', 'dynamicconditions' ),
                    'greater' => __( 'Greater than', 'dynamicconditions' ),
                ],
                'render_type' => 'none',
                'description' => __( 'Select your condition for this widget visibility.', 'dynamicconditions' ),
            ],
            [
                'overwrite' => true,
            ]
        );
        $element->add_control(
            'dynamicconditions_value',
            [
                'type' => Controls_Manager::TEXTAREA,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'description' => __( 'Add your conditional value here if you selected equal to, not equal to or contains on the selection above.', 'dynamicconditions' ),

                'condition' => [
                    'dynamicconditions_condition' => [ 'equal', 'not_equal', 'contains', 'not_contains', 'less', 'greater' ],
                ],
            ],
            [
                'overwrite' => true,
            ]
        );

        $element->end_controls_section();
    }
}