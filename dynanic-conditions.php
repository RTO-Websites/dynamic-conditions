<?php

/*
 * Plugin Name: Dynamic Conditions
 * Plugin URI:        https://github.com/RTO-Websites/dynamic-conditions
 * Description:       Adds conditions for dynamic tags
 * Version:           0.0.1
 * Author:            RTO GmbH
 * Author URI:        https://www.rto.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dynamic-conditions
 * Domain Path:       /languages
*/

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;


add_action( 'elementor/element/after_section_end', 'dynamic_conditions_elementor_section', 10, 3 );

function dynamic_conditions_elementor_section( $element, $section_id, $args ) {
    if ( false && !Elementor\Plugin::$instance->editor->is_edit_mode() ) {
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
            'tab' => Elementor\Controls_Manager::TAB_ADVANCED,
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
            'type' => Controls_Manager::GALLERY,
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
            'type' => Elementor\Controls_Manager::SELECT,
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
            'type' => Elementor\Controls_Manager::SELECT2,
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
            'type' => Elementor\Controls_Manager::TEXTAREA,
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


// website

add_action( 'elementor/widget/render_content', 'dynamic_conditions_elementor_render', 10, 2 );

function dynamic_conditions_elementor_render( $content, $widget ) {
    if ( Elementor\Plugin::$instance->editor->is_edit_mode() ) {
        return $content;
    }

    $settings = $widget->get_settings_for_display();
    $controls = $widget->get_controls();

    if ( empty( $settings['dynamicconditions_condition'] ) ) {
        // no condition selected - disable conditions
        return $content;
    }

    /*$dynamic_settings = array_merge( $control_obj->get_settings( 'dynamic' ), $control['dynamic'] );

    if ( ! empty( $dynamic_settings['active'] ) && ! empty( $all_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control_name ] ) ) {
        $parsed_value = $control_obj->parse_tags( $all_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control_name ], $dynamic_settings );*/

    $checkValue = $settings['dynamicconditions_value'];
    $widgetValue = $settings['dynamiccondtions_dynamic'];

    $condition = false;
    switch ( $settings['dynamicconditions_condition'] ) {
        case 'equal':
            $condition = $checkValue == $widgetValue;
            break;

        case 'not_equal':
            $condition = $checkValue != $widgetValue;
            break;

        case 'contains':
            $condition = strpos( $widgetValue, $checkValue ) !== false;
            break;

        case 'not_contains':
            $condition = strpos( $widgetValue, $checkValue ) === false;
            break;

        case 'empty':
            $condition = empty( $widgetValue );
            break;

        case 'not_empty':
            $condition = !empty( $widgetValue );
            break;
    }

    $hide = false;

    switch ( $settings['dynamicconditions_visibility'] ) {
        case 'show':
            if ( !$condition ) {
                $hide = true;
            }
            break;
        case 'hide':
        default:
            if ( $condition ) {
                $hide = true;
            }
            break;
    }

    if ( $hide ) {
        return '<!-- hidden widget -->';
    }

    return $content;
}
