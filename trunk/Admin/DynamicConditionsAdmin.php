<?php

namespace DynamicConditions\Admin;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;
use DynamicConditions\Lib\Date;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    DynamicConditions
 * @subpackage DynamicConditions/admin
 */

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die;
}

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
     * @param string $pluginName The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
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

        wp_enqueue_style( $this->pluginName, DynamicConditions_URL . '/Admin/css/dynamic-conditions-admin.css', [], $this->version, 'all' );

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
        $valueCondition = [
            'equal',
            'not_equal',
            'contains',
            'not_contains',
            'less',
            'greater',
            'between',
            'in_array',
            'in_array_contains'
        ];

        $allCondition = [
            'equal',
            'not_equal',
            'contains',
            'not_contains',
            'less',
            'greater',
            'between',
            'empty',
            'not_empty'
        ];

        $type = 'element';
        $renderType = 'ui';
        if ( !empty( $element ) && is_object( $element ) && method_exists( $element, 'get_type' ) ) {
            $type = $element->get_type();
        }

        $categories = [
            Module::BASE_GROUP,
            Module::TEXT_CATEGORY,
            Module::URL_CATEGORY,
            Module::GALLERY_CATEGORY,
            Module::IMAGE_CATEGORY,
            Module::MEDIA_CATEGORY,
            Module::POST_META_CATEGORY,
        ];

        $categoriesTextOnly = [
            Module::BASE_GROUP,
            Module::TEXT_CATEGORY,
            Module::URL_CATEGORY,
            Module::POST_META_CATEGORY,
        ];

        if ( defined( Module::class . '::COLOR_CATEGORY' ) ) {
            $categories[] = Module::COLOR_CATEGORY;
        }

        $element->start_controls_section(
            'dynamicconditions_section',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => __( 'Dynamic Conditions', 'dynamicconditions' ),
            ]
        );

        $element->add_control(
            'dynamicconditions_dynamic',
            [
                'label' => __( 'Dynamic Tag', 'dynamicconditions' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                    'categories' => $categories,
                ],
                'render_type' => $renderType,
                'placeholder' => __( 'Select condition field', 'dynamicconditions' ),
            ]
        );

        $element->add_control(
            'dynamicconditions_visibility',
            [
                'label' => __( 'Show/Hide', 'dynamicconditions' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'hide',
                'options' => [
                    'show' => __( 'Show when condition met', 'dynamicconditions' ),
                    'hide' => __( 'Hide when condition met', 'dynamicconditions' ),
                ],
                'render_type' => $renderType,
                'separator' => 'before',
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
                    'in_array' => __( 'In array', 'dynamicconditions' ),
                    'in_array_contains' => __( 'In array contains', 'dynamicconditions' ),
                ],
                'description' => __( 'Select your condition for this widget visibility.', 'dynamicconditions' ),

                'prefix_class' => 'dc-has-condition dc-condition-',
                'render_type' => 'template',
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
                'render_type' => $renderType,
                'description' => __( 'Select what do you want to compare', 'dynamicconditions' ),
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
                'render_type' => $renderType,

                'dynamic' => [
                    'active' => true,
                    'categories' => $categoriesTextOnly,
                ],
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
                'render_type' => $renderType,
                'dynamic' => [
                    'active' => true,
                    'categories' => $categoriesTextOnly,
                ],

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
                'render_type' => $renderType,

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
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => [ 'between' ],
                    'dynamicconditions_type' => 'date',
                ],
            ]
        );

        $element->add_control(
            'dynamicconditions_day_array_value',
            [
                'type' => Controls_Manager::SELECT2,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => [ 'in_array' ],
                    'dynamicconditions_type' => 'days',
                ],
                'description' => __( 'Add your conditional value to compare here.', 'dynamicconditions' ),
                'options' => Date::getDaysTranslated(),
                'multiple' => true,
            ]
        );
        $element->add_control(
            'dynamicconditions_day_value',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => array_diff( $valueCondition, [ 'in_array' ] ),
                    'dynamicconditions_type' => 'days',
                ],
                'description' => __( 'Add your conditional value to compare here.', 'dynamicconditions' ),
                'options' => Date::getDaysTranslated(),
            ]
        );

        $element->add_control(
            'dynamicconditions_day_value2',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => [ 'between' ],
                    'dynamicconditions_type' => 'days',
                ],
                'description' => __( 'Add a second condition value, if between is selected', 'dynamicconditions' ),
                'options' => Date::getDaysTranslated(),
            ]
        );

        $element->add_control(
            'dynamicconditions_month_array_value',
            [
                'type' => Controls_Manager::SELECT2,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => [ 'in_array' ],
                    'dynamicconditions_type' => 'months',
                ],
                'description' => __( 'Add your conditional value to compare here.', 'dynamicconditions' ),
                'options' => Date::getMonthsTranslated(),
                'multiple' => true,
            ]
        );

        $element->add_control(
            'dynamicconditions_month_value',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Conditional value', 'dynamicconditions' ),
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => array_diff( $valueCondition, [ 'in_array' ] ),
                    'dynamicconditions_type' => 'months',
                ],
                'description' => __( 'Add your conditional value to compare here.', 'dynamicconditions' ),
                'options' => Date::getMonthsTranslated(),
            ]
        );

        $element->add_control(
            'dynamicconditions_month_value2',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => [ 'between' ],
                    'dynamicconditions_type' => 'months',
                ],
                'description' => __( 'Add a second condition value, if between is selected', 'dynamicconditions' ),
                'options' => Date::getMonthsTranslated(),
            ]
        );


        $element->add_control(
            'dynamicconditions_in_array_description',
            [
                'type' => Controls_Manager::RAW_HTML,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => [ 'in_array' ],
                ],
                'show_label' => false,
                'raw' => __( 'Use comma-separated values, to check if dynamic-value is equal with one of each item.', 'dynamicconditions' ),
            ]
        );

        $element->add_control(
            'dynamicconditions_in_array_contains_description',
            [
                'type' => Controls_Manager::RAW_HTML,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => [ 'in_array_contains' ],
                ],
                'show_label' => false,
                'raw' => __( 'Use comma-separated values, to check if dynamic-value contains one of each item.', 'dynamicconditions' ),
            ]
        );

        $languageArray = explode( '_', get_locale() );
        $language = array_shift( $languageArray );
        $element->add_control(
            'dynamicconditions_date_description',
            [
                'type' => Controls_Manager::RAW_HTML,
                'label' => __( 'Conditional value', 'dynamicconditions' ) . ' 2',
                'render_type' => $renderType,
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
                'render_type' => $renderType,
                'condition' => [
                    'dynamicconditions_condition' => $allCondition,
                ],
            ]
        );

        if ( $type === 'column' ) {
            $element->add_control(
                'dynamicconditions_resizeOtherColumns',
                [
                    'type' => Controls_Manager::SWITCHER,
                    'label' => __( 'Resize other columns', 'dynamicconditions' ),
                    'render_type' => $renderType,
                    'condition' => [
                        'dynamicconditions_condition' => $allCondition,
                        'dynamicconditions_hideContentOnly!' => 'on',
                    ],
                    'return_value' => 'on',
                ]
            );
        }


        $element->add_control(
            'dynamicconditions_headline_expert',
            [
                'label' => __( 'Expert', 'dynamicconditions' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $element->add_control(
            'dynamicconditions_parse_shortcodes',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'Parse shortcodes', 'dynamicconditions' ),
                'render_type' => $renderType,
            ]
        );

        $element->add_control(
            'dynamicconditions_prevent_date_parsing',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __( 'Prevent date parsing', 'dynamicconditions' ),
                'render_type' => $renderType,
            ]
        );


        $element->add_control(
            'dynamicconditions_hr3',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );


        $element->add_control(
            'dynamicconditions_hideWrapper',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __( 'Hide wrapper', 'dynamicconditions' ),
                'description' => __( 'Will hide a parent matching the selector.', 'dynamicconditions' ),
                'placeholder' => 'selector',
                'render_type' => $renderType,
            ]
        );

        $element->add_control(
            'dynamicconditions_hideOthers',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __( 'Hide other elements', 'dynamicconditions' ),
                'description' => __( 'Will hide all other elements matching the selector.', 'dynamicconditions' ),
                'placeholder' => 'selector',
                'render_type' => $renderType,
            ]
        );

        $element->add_control(
            'dynamicconditions_hr4',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );

        $element->add_control(
            'dynamicconditions_widget_id',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __( 'Widget-ID', 'dynamicconditions' ),
                'render_type' => $renderType,
                'description' => '<script>
                    $dcWidgetIdInput = jQuery(\'.elementor-control-dynamicconditions_widget_id input\');
                    $dcWidgetIdInput.val(elementor.getCurrentElement().model.id);
                    $dcWidgetIdInput.attr(\'readonly\', true);
                    $dcWidgetIdInput.on(\'focus click\', function() { this.select();document.execCommand(\'copy\'); });
                    </script>',
            ]
        );

        $element->add_control(
            'dynamicconditions_hr5',
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
                'render_type' => $renderType,
            ]
        );

        $element->end_controls_section();
    }
}
