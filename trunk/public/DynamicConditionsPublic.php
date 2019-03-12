<?php namespace Pub;

use Lib\DynamicConditionsDate;

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

    private $elementSettings = [];

    private $dateInstance;

    private static $debugCssRendered = false;

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
        $this->dateInstance = new DynamicConditionsDate();

    }

    /**
     * Gets settings with english locale (needed for date)
     *
     * @param $element
     * @return mixed
     */
    private function getElementSettings( $element ) {
        $id = $element->get_id();
        if ( !empty( $this->elementSettings[$id] ) ) {
            // dont work in a loop?
            //return $this->elementSettings[$id];
        }

        // set locale to english, for better parsing
        $currentLocale = setlocale( LC_ALL, 0 );
        setlocale( LC_ALL, 'en_GB' );

        add_filter( 'date_i18n', [ $this->dateInstance, 'filterDateI18n' ], 10, 4 );
        add_filter( 'get_the_date', [ $this->dateInstance, 'filterPostDate' ], 10, 3 );
        add_filter( 'get_the_modified_date', [ $this->dateInstance, 'filterPostDate' ], 10, 3 );
        $this->elementSettings[$id] = $element->get_settings_for_display();
        remove_filter( 'date_i18n', [ $this->dateInstance, 'filterDateI18n' ] );
        remove_filter( 'get_the_date', [ $this->dateInstance, 'filterPostDate' ] );
        remove_filter( 'get_the_modified_date', [ $this->dateInstance, 'filterPostDate' ] );

        // reset locale
        setlocale( LC_ALL, $currentLocale );

        $this->elementSettings[$id]['dtData'] = [
            'id' => $id,
            'type' => $element->get_type(),
        ];

        return $this->elementSettings[$id];
    }

    /**
     * Check if section is hidden, before rendering
     *
     * @param $section
     */
    public function filterSectionContentBefore( $section ) {
        $settings = $this->getElementSettings( $section );
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
        if ( empty( $section->dynamicConditionIsHidden ) ) {
            return;
        }

        ob_end_clean();

        $type = $section->get_type();
        $settings = $this->getElementSettings( $section );

        if ( !empty( $section->get_settings( 'dynamicconditions_hideContentOnly' ) ) ) {
            // render wrapper
            $section->before_render();
            $section->after_render();
        } else if ( $type == 'column' && $settings['dynamicconditions_resizeOtherColumns'] ) {
            echo '<div class="dc-elementor-hidden-column" data-size="' . $settings['_inline_size'] . '"></div>';
        }

        echo '<!-- hidden ' . $type . ' -->';
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

        // loop values
        $condition = $this->loopValues( $settings );

        $hide = false;

        $visibility = self::checkEmpty( $settings, 'dynamicconditions_visibility', 'hide' );
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

    /**
     * Loop widget-values and check the condition
     *
     * @param $settings
     * @return bool|mixed
     */
    private function loopValues( $settings ) {
        $condition = false;
        $dynamicTagValueArray = self::checkEmpty( $settings, 'dynamicconditions_dynamic' );

        if ( !is_array( $dynamicTagValueArray ) ) {
            $dynamicTagValueArray = [ $dynamicTagValueArray ];
        }

        // get value form conditions
        $compareType = self::checkEmpty( $settings, 'dynamicconditions_type', 'default' );
        list( $checkValue, $checkValue2 ) = $this->getCheckValue( $compareType, $settings );

        foreach ( $dynamicTagValueArray as $dynamicTagValue ) {
            if ( is_array( $dynamicTagValue ) ) {
                if ( !empty( $dynamicTagValue['id'] ) ) {
                    $dynamicTagValue = get_attachment_link( $dynamicTagValue['id'] );
                } else {
                    continue;
                }
            }

            // parse value based on compare-type
            $this->parseWidgetValue( $dynamicTagValue, $compareType );


            // compare widget-value with check-values
            list( $condition, $break, $breakFalse )
                = $this->compareValues( $settings['dynamicconditions_condition'], $dynamicTagValue, $checkValue, $checkValue2 );

            // debug output
            $this->renderDebugInfo( $settings, $dynamicTagValue, $checkValue, $checkValue2 );

            if ( $break && $condition ) {
                // break if condition is true
                break;
            }

            if ( $breakFalse && !$condition ) {
                // break if condition is false
                break;
            }
        }

        return $condition;
    }

    /**
     * Compare values
     *
     * @param $compare
     * @param $widgetValue
     * @param $checkValue
     * @param $checkValue2
     * @return array
     */
    private function compareValues( $compare, $widgetValue, $checkValue, $checkValue2 ) {
        $break = false;
        $breakFalse = false;
        $condition = false;

        switch ( $compare ) {
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
                    break;
                }
                $condition = strpos( $widgetValue, $checkValue ) !== false;
                $break = true;
                break;

            case 'not_contains':
                if ( empty( $checkValue ) ) {
                    break;
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

            case 'less':
                if ( is_numeric( $widgetValue ) ) {
                    $condition = $widgetValue < $checkValue;
                } else {
                    $condition = strlen( $widgetValue ) < strlen( $checkValue );
                }
                $break = true;
                break;

            case 'greater':
                if ( is_numeric( $widgetValue ) ) {
                    $condition = $widgetValue > $checkValue;
                } else {
                    $condition = strlen( $widgetValue ) > strlen( $checkValue );
                }
                $break = true;
                break;

            case 'between':
                $condition = $widgetValue >= $checkValue && $widgetValue <= $checkValue2;
                $break = true;
                break;
        }

        return [
            $condition,
            $break,
            $breakFalse,
        ];
    }

    /**
     * Parse value of widget to timestamp, day or month
     *
     * @param $widgetValue
     * @param $compareType
     */
    private function parseWidgetValue( &$widgetValue, $compareType ) {
        switch ( $compareType ) {
            case 'days':
                $widgetValue = date( 'N', DynamicConditionsDate::stringToTime( $widgetValue ) );
                break;

            case 'months':
                $widgetValue = date( 'n', DynamicConditionsDate::stringToTime( $widgetValue ) );
                break;

            case 'strtotime':
                // nobreak
            case 'date':
                $widgetValue = DynamicConditionsDate::stringToTime( $widgetValue );
                break;
        }
    }

    /**
     * Get value to compare
     *
     * @param $compareType
     * @param $settings
     * @return array
     */
    private function getCheckValue( $compareType, $settings ) {
        switch ( $compareType ) {
            case 'days':
                $checkValue = self::checkEmpty( $settings, 'dynamicconditions_day_value' );
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_day_value2' );
                $checkValue = DynamicConditionsDate::unTranslateDate( $checkValue );
                $checkValue2 = DynamicConditionsDate::unTranslateDate( $checkValue2 );
                break;

            case 'months':
                $checkValue = self::checkEmpty( $settings, 'dynamicconditions_month_value' );
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_month_value2' );
                $checkValue = DynamicConditionsDate::unTranslateDate( $checkValue );
                $checkValue2 = DynamicConditionsDate::unTranslateDate( $checkValue2 );
                break;

            case 'date':
                $checkValue = self::checkEmpty( $settings, 'dynamicconditions_date_value' );
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_date_value2' );
                $checkValue = DynamicConditionsDate::stringToTime( $checkValue );
                $checkValue2 = DynamicConditionsDate::stringToTime( $checkValue2 );
                break;

            case 'strtotime':
                $checkValue = self::checkEmpty( $settings, 'dynamicconditions_value' );
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_value2' );
                $checkValue = DynamicConditionsDate::unTranslateDate( $checkValue );
                $checkValue2 = DynamicConditionsDate::unTranslateDate( $checkValue2 );
                $checkValue = DynamicConditionsDate::stringToTime( $checkValue );
                $checkValue2 = DynamicConditionsDate::stringToTime( $checkValue2 );
                break;

            case 'default':
            default:
                $checkValue = self::checkEmpty( $settings, 'dynamicconditions_value' );
                $checkValue2 = self::checkEmpty( $settings, 'dynamicconditions_value2' );
                break;
        }

        return [
            $checkValue,
            $checkValue2,
        ];
    }

    /**
     * Checks if an array or entry in array is empty and return its value
     *
     * @param array $array
     * @param null $key
     * @return array|mixed|null
     */
    public static function checkEmpty( $array = [], $key = null, $fallback = null ) {
        if ( empty( $key ) ) {
            return !empty( $array ) ? $array : $fallback;
        }

        return !empty( $array[$key] ) ? $array[$key] : $fallback;
    }

    /**
     * Renders debug info
     *
     * @param $settings
     * @param $dynamicTagValue
     * @param $checkValue
     * @param $checkValue2
     */
    private function renderDebugInfo( $settings, $dynamicTagValue, $checkValue, $checkValue2 ) {
        if ( !$settings['debug'] ) {
            return;
        }

        $visibility = self::checkEmpty( $settings, 'dynamicconditions_visibility', 'hide' );

        include( 'partials/debug.php' );

        $this->renderDebugCss();
    }

    /**
     * Renders css for debug-output
     */
    private function renderDebugCss() {
        if ( self::$debugCssRendered ) {
            return;
        }
        self::$debugCssRendered = true;

        echo '<style>';
        include( 'css/debug.css' );
        echo '</style>';
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueueScripts() {
        wp_enqueue_script( $this->pluginName, plugin_dir_url( __FILE__ ) . 'js/dynamic-conditions-public.js', [ 'jquery' ], $this->version, true );
    }

}
