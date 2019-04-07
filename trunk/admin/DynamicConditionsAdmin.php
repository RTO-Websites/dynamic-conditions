<?php namespace Admin;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;
use Lib\DynamicConditionsDate;


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
        $valueCondition = [ 'equal', 'not_equal', 'contains', 'not_contains', 'less', 'greater', 'between' ];
        $type = 'element';
        if ( !empty( $element ) && is_object( $element ) && method_exists( $element, 'get_type' ) ) {
            $type = $element->get_type();
        }

        $element->start_controls_section(
            'dynamicconditions_section',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => __( 'Dynamic Conditions', 'dynamicconditions' ),
            ]
        );

        $element->add_control(
            'dynamicconditions_visibility',
            [
                'label' => __( 'Show/Hide', 'dynamicconditions' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'hide',
                'options' => [
                    'show' => __( 'Show if condition met', 'dynamicconditions' ),
                    'hide' => __( 'Hide if condition met', 'dynamicconditions' ),
                ],
                'render_type' => 'ui',
                'separator' => 'after',
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
                        Module::BASE_GROUP,
                        Module::TEXT_CATEGORY,
                        Module::URL_CATEGORY,
                        Module::GALLERY_CATEGORY,
                        Module::IMAGE_CATEGORY,
                        Module::MEDIA_CATEGORY,
                        Module::POST_META_CATEGORY,
                    ],
                    'render_type' => 'none',
                ],
                'returnType' => 'array',
                'render_type' => 'ui',
                'placeholder' => __( 'Select condition field', 'dynamiccondtions' ),
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
                    'between' => __( 'Between', 'dynamicconditions' ),
                    'less' => __( 'Less than', 'dynamicconditions' ),
                    'greater' => __( 'Greater than', 'dynamicconditions' ),
                ],
                'render_type' => 'none',
                'description' => __( 'Select your condition for this widget visibility.', 'dynamicconditions' ),
                'condition' => [
                ],
            ]
        );

        $element->add_control(
            'dynamicconditions_type',
            [
                'label' => __( 'Compare Type', 'dynamicconditions' ),
                'type' => Controls_Manager::SELECT,
                'multiple' => false,
                'label_block' => true,
                'options' => [
                    'default' => __( 'Text', 'dynamicconditions' ),
                    'date' => __( 'Date', 'dynamicconditions' ),
                    'days' => __( 'Weekdays', 'dynamicconditions' ),
                    'months' => __( 'Months', 'dynamicconditions' ),
                    'strtotime' => __( 'String to time', 'dynamicconditions' ),
                ],
                'default' => 'default',
                'render_type' => 'none',
                'description' => __( 'Select what to you want to compare', 'dynamicconditions' ),
                'condition' => [
                    'dynamicconditions_condition' => $valueCondition,
                ],
            ]
        );

        $element->add_control(
            'dynamicconditions_value',
            [
                'type' => Controls_Manager::TEXTAREA,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'description' => __( 'Add your conditional value to compare here.', 'dynamicconditions' ),
                'render_type' => 'none',

                'condition' => [
                    'dynamicconditions_condition' => $valueCondition,
                    'dynamicconditions_type' => [ 'default', 'strtotime' ],
                ],
            ]
        );

        $element->add_control(
            'dynamicconditions_value2',
            [
                'type' => Controls_Manager::TEXTAREA,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'description' => __( 'Add a second condition value, if between is selected', 'dynamicconditions' ),
                'render_type' => 'none',

                'condition' => [
                    'dynamicconditions_condition' => [ 'between' ],
                    'dynamicconditions_type' => [ 'default', 'strtotime' ],
                ],
            ]
        );


        $element->add_control(
            'dynamicconditions_date_value',
            [
                'type' => Controls_Manager::DATE_TIME,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'description' => __( 'Add your conditional value to compare here.', 'dynamicconditions' ),
                'render_type' => 'none',

                'condition' => [
                    'dynamicconditions_condition' => $valueCondition,
                    'dynamicconditions_type' => 'date',
                ],
            ]
        );

        $element->add_control(
            'dynamicconditions_date_value2',
            [
                'type' => Controls_Manager::DATE_TIME,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'description' => __( 'Add a second condition value, if between is selected', 'dynamicconditions' ),
                'render_type' => 'none',
                'condition' => [
                    'dynamicconditions_condition' => [ 'between' ],
                    'dynamicconditions_type' => 'date',
                ],
            ]
        );

        $element->add_control(
            'dynamicconditions_day_value',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'render_type' => 'none',
                'condition' => [
                    'dynamicconditions_condition' => $valueCondition,
                    'dynamicconditions_type' => 'days',
                ],
                'description' => __( 'Add your conditional value to compare here.', 'dynamicconditions' ),
                'options' => DynamicConditionsDate::getDaysTranslated(),
            ]
        );

        $element->add_control(
            'dynamicconditions_day_value2',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'render_type' => 'none',
                'condition' => [
                    'dynamicconditions_condition' => [ 'between' ],
                    'dynamicconditions_type' => 'days',
                ],
                'description' => __( 'Add a second condition value, if between is selected', 'dynamicconditions' ),
                'options' => DynamicConditionsDate::getDaysTranslated(),
            ]
        );

        $element->add_control(
            'dynamicconditions_month_value',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'render_type' => 'none',
                'condition' => [
                    'dynamicconditions_condition' => $valueCondition,
                    'dynamicconditions_type' => 'months',
                ],
                'description' => __( 'Add your conditional value to compare here.', 'dynamicconditions' ),
                'options' => DynamicConditionsDate::getMonthsTranslated(),
            ]
        );

        $element->add_control(
            'dynamicconditions_month_value2',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'render_type' => 'none',
                'condition' => [
                    'dynamicconditions_condition' => [ 'between' ],
                    'dynamicconditions_type' => 'months',
                ],
                'description' => __( 'Add a second condition value, if between is selected', 'dynamicconditions' ),
                'options' => DynamicConditionsDate::getMonthsTranslated(),
            ]
        );

        $languageArray = explode( '_', get_locale() );
        $language = array_shift( $languageArray );
        $element->add_control(
            'dynamicconditions_date_description',
            [
                'type' => Controls_Manager::RAW_HTML,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'render_type' => 'none',
                'condition' => [
                    'dynamicconditions_condition' => $valueCondition,
                    'dynamicconditions_type' => 'strtotime',
                ],
                'show_label' => false,
                'raw' => '<div class="elementor-control-field-description">'
                    . '<a href="https://php.net/manual/' . $language . '/function.strtotime.php" target="_blank">'
                    . __( 'Supported Date and Time Formats', 'dynamicconditions' ) . '</a></div>',
            ]
        );

        $element->add_control(
            'dynamicconditions_hr',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
                'condition' => [
                    'dynamicconditions_condition' => $valueCondition,
                ],
            ]
        );

        $element->add_control(
            'dynamicconditions_hideContentOnly',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'Hide only content', 'dynamicconditions' ),
                'description' => __( 'If checked, only the inner content will be hidden, so you will see an empty section', 'dynamicconditions' ),
                'return_value' => 'on',
                'render_type' => 'none',
                'condition' => [
                    'dynamicconditions_condition' => $valueCondition,
                ],
            ]
        );

        if ( $type === 'column' ) {
            $element->add_control(
                'dynamicconditions_resizeOtherColumns',
                [
                    'type' => Controls_Manager::SWITCHER,
                    'label' => __( 'Resize other columns', 'dynamicconditions' ),
                    'render_type' => 'none',
                    'condition' => [
                        'dynamicconditions_condition' => $valueCondition,
                        'dynamicconditions_hideContentOnly!' => 'on',
                    ],
                    'return_value' => 'on',
                ]
            );
        }

        $element->add_control(
            'dynamicconditions_hr2',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $element->add_control(
            'dynamicconditions_debug',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'Debug-Mode', 'dynamicconditions' ),
                'render_type' => 'none',
            ]
        );
        $element->end_controls_section();
    }

}
