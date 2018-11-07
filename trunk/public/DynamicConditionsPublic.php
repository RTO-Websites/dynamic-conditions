<?php namespace Pub;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    DynamicConditions
 * @subpackage DynamicConditions/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    DynamicConditions
 * @subpackage DynamicConditions/public
 * @author     RTO GmbH <kundenhomepage@rto.de>
 */
class DynamicConditionsPublic {

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
     * @param      string $pluginName The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct( $pluginName, $version ) {

        $this->pluginName = $pluginName;
        $this->version = $version;

    }


    /**
     * Stopp rendering of widget if its hidden
     *
     * @param $content
     * @param $widget
     * @return string
     */
    public function filterWidgetContent( $content, $widget = null ) {
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || empty( $widget ) ) {
            return $content;
        }

        $settings = $widget->get_settings_for_display();

        $hide = $this->checkCondition( $settings );


        if ( $hide ) {
            return '<!-- hidden widget -->';
        }

        return $content;
    }

    /**
     * Check if section is hidden, before rendering
     *
     * @param $section
     */
    public function filterSectionContentBefore( $section ) {
        $settings = $section->get_settings_for_display();
        $hide = $this->checkCondition( $settings );

        if ( !$hide ) {
            return;
        }

        $section->dynamicConditionIsHidden = true;

        ob_start();
    }

    /**
     * Clean output of section if it is hidden
     *
     * @param $section
     */
    public function filterSectionContentAfter( $section ) {
        if ( !empty( $section->dynamicConditionIsHidden ) ) {
            ob_clean();
            echo '<!-- hidden section -->';
        }
    }


    /**
     * Checks condition, return if element is hidden
     *
     * @param $settings
     * @return bool
     */
    public function checkCondition( $settings ) {

        if ( empty( $settings['dynamicconditions_condition'] )
        ) {
            // no condition selected - disable conditions
            return false;
        }

        $checkValue = !empty( $settings['dynamicconditions_value'] ) ? $settings['dynamicconditions_value'] : '';
        $widgetValueArray = !empty( $settings['dynamicconditions_dynamic'] ) ? $settings['dynamicconditions_dynamic'] : '';

        if ( !is_array( $widgetValueArray ) ) {
            $widgetValueArray = [ $widgetValueArray ];
        }

        $condition = false;
        $break = false;
        $breakFalse = false;
        foreach ( $widgetValueArray as $widgetValue ) {
            if ( is_array( $widgetValue ) ) {
                if ( !empty( $widgetValue['id'] ) ) {
                    $widgetValue = get_attachment_link( $widgetValue['id'] );
                } else {
                    continue;
                }
            }

            switch ( $settings['dynamicconditions_condition'] ) {
                case 'equal':
                    $condition = $checkValue == $widgetValue;
                    $break = true;
                    break;

                case 'not_equal':
                    $condition = $checkValue != $widgetValue;
                    $breakFalse = true;
                    break;

                case 'contains':
                    if ( empty( $checkValue ) ) {
                        continue 2;
                    }
                    $condition = strpos( $widgetValue, $checkValue ) !== false;
                    $break = true;
                    break;

                case 'not_contains':
                    if ( empty( $checkValue ) ) {
                        continue 2;
                    }
                    $condition = strpos( $widgetValue, $checkValue ) === false;
                    $breakFalse = true;
                    break;

                case 'empty':
                    $condition = empty( $widgetValue );
                    $breakFalse = true;
                    break;

                case 'not_empty':
                    $condition = !empty( $widgetValue );
                    $break = true;
                    break;
            }

            if ( $break && $condition ) {
                // break if condition is true
                break;
            }
            if ( $breakFalse && !$condition ) {
                // break if condition is false
                break;
            }
        }

        $hide = false;

        $visibility = !empty( $settings['dynamicconditions_visibility'] ) ? $settings['dynamicconditions_visibility'] : 'hide';
        switch ( $visibility ) {
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

        return $hide;
    }
}
