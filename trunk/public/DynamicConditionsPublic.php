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

        $clonedElement = clone $element;

        $element->get_settings_for_display(); // call to cache settings

        // set locale to english, for better parsing
        $currentLocale = setlocale( LC_ALL, 0 );
        setlocale( LC_ALL, 'en_GB' );

        add_filter( 'date_i18n', [ $this->dateInstance, 'filterDateI18n' ], 10, 4 );
        add_filter( 'get_the_date', [ $this->dateInstance, 'filterPostDate' ], 10, 3 );
        add_filter( 'get_the_modified_date', [ $this->dateInstance, 'filterPostDate' ], 10, 3 );
        $fields = '__dynamic__
            dynamicconditions_dynamic
            dynamicconditions_condition
            dynamicconditions_type
            dynamicconditions_resizeOtherColumns
            dynamicconditions_hideContentOnly
            dynamicconditions_visibility
            dynamicconditions_day_value
            dynamicconditions_day_value2
            dynamicconditions_month_value
            dynamicconditions_month_value2
            dynamicconditions_date_value
            dynamicconditions_date_value2
            dynamicconditions_value
            dynamicconditions_value2
            dynamicconditions_debug
            _inline_size';

        foreach ( explode( "\n", $fields ) as $field ) {
            $field = trim( $field );
            $this->elementSettings[$id][$field] = $clonedElement->get_settings_for_display( $field );
        }
        unset( $clonedElement );

        remove_filter( 'date_i18n', [ $this->dateInstance, 'filterDateI18n' ], 10 );
        remove_filter( 'get_the_date', [ $this->dateInstance, 'filterPostDate' ], 10 );
        remove_filter( 'get_the_modified_date', [ $this->dateInstance, 'filterPostDate' ], 10 );

        // reset locale
        setlocale( LC_ALL, $currentLocale );


        $tagData = $this->getDynamicTagData( $id );

        $this->elementSettings[$id]['dynamicConditionsData'] = [
            'id' => $id,
            'type' => $element->get_type(),
            'name' => $element->get_name(),
            'selectedTag' => $tagData['selectedTag'],
            'tagData' => $tagData['tagData'],
            'tagKey' => $tagData['tagKey'],
        ];

        return $this->elementSettings[$id];
    }

    /**
     * Returns
     *
     * @param $id
     * @return array
     */
    private function getDynamicTagData( $id ): array {

        if ( empty( $this->elementSettings[$id]['__dynamic__'] )
            || empty( $this->elementSettings[$id]['__dynamic__']['dynamicconditions_dynamic'] )
        ) {
            // no dynamic tag set
            return [
                'selectedTag' => null,
                'tagData' => null,
                'tagKey' => null,
            ];
        }

        $selectedTag = null;
        $tagSettings = null;
        $tagKey = null;

        $tag = $this->elementSettings[$id]['__dynamic__']['dynamicconditions_dynamic'];
        $splitTag = explode( ' name="', $tag );
        #var_dump($splitTag);

        // get selected tag
        if ( !empty( $splitTag[1] ) ) {
            $splitTag2 = explode( '"', $splitTag[1] );
            $selectedTag = $splitTag2[0];
        }

        // get tag settings
        if ( strpos( $selectedTag, 'acf-' ) === 0 ) {
            $splitTag = explode( ' settings="', $tag );
            if ( !empty( $splitTag[1] ) ) {
                $splitTag2 = explode( '"', $splitTag[1] );
                $tagSettings = json_decode( urldecode( $splitTag2[0] ), true );
                if ( !empty( $tagSettings['key'] ) ) {
                    $tagKey = $tagSettings['key'];
                    $fieldData = get_field_object( explode( ':', $tagSettings['key'] )[0], false, false );
                    var_dump( $fieldData );
                }
            }
        }

        return [
            'selectedTag' => $selectedTag,
            'tagData' => $tagSettings,
            'tagKey' => $tagKey,
        ];

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

        if ( !empty( $settings['dynamicconditions_hideContentOnly'] ) ) {
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
        if ( empty( $settings['dynamicconditions_condition'] ) || empty( $settings['dynamicConditionsData']['selectedTag'] )
        ) {
            // no condition or no tag selected - disable conditions
            return false;
        }

        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
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

        $debugValue = '';

        foreach ( $dynamicTagValueArray as $dynamicTagValue ) {
            if ( is_array( $dynamicTagValue ) ) {
                if ( !empty( $dynamicTagValue['id'] ) ) {
                    $dynamicTagValue = get_attachment_link( $dynamicTagValue['id'] );
                } else {
                    continue;
                }
            }

            // parse value based on compare-type
            $this->parseDynamicTagValue( $dynamicTagValue, $compareType );

            $debugValue .= $dynamicTagValue . '<br />';

            // compare widget-value with check-values
            list( $condition, $break, $breakFalse )
                = $this->compareValues( $settings['dynamicconditions_condition'], $dynamicTagValue, $checkValue, $checkValue2 );


            if ( $break && $condition ) {
                // break if condition is true
                break;
            }

            if ( $breakFalse && !$condition ) {
                // break if condition is false
                break;
            }
        }

        // debug output
        $this->renderDebugInfo( $settings, $debugValue, $checkValue, $checkValue2, $condition );

        return $condition;
    }

    /**
     * Compare values
     *
     * @param $compare
     * @param $dynamicTagValue
     * @param $checkValue
     * @param $checkValue2
     * @return array
     */
    private function compareValues( $compare, $dynamicTagValue, $checkValue, $checkValue2 ) {
        $break = false;
        $breakFalse = false;
        $condition = false;

        switch ( $compare ) {
            case 'equal':
                $condition = $checkValue == $dynamicTagValue;
                $break = true;
                break;

            case 'not_equal':
                $condition = $checkValue != $dynamicTagValue;
                $breakFalse = true;
                break;

            case 'contains':
                if ( empty( $checkValue ) ) {
                    break;
                }
                $condition = strpos( $dynamicTagValue, $checkValue ) !== false;
                $break = true;
                break;

            case 'not_contains':
                if ( empty( $checkValue ) ) {
                    break;
                }
                $condition = strpos( $dynamicTagValue, $checkValue ) === false;
                $breakFalse = true;
                break;

            case 'empty':
                $condition = empty( $dynamicTagValue );
                $breakFalse = true;
                break;

            case 'not_empty':
                $condition = !empty( $dynamicTagValue );
                $break = true;
                break;

            case 'less':
                if ( is_numeric( $dynamicTagValue ) ) {
                    $condition = $dynamicTagValue < $checkValue;
                } else {
                    $condition = strlen( $dynamicTagValue ) < strlen( $checkValue );
                }
                $break = true;
                break;

            case 'greater':
                if ( is_numeric( $dynamicTagValue ) ) {
                    $condition = $dynamicTagValue > $checkValue;
                } else {
                    $condition = strlen( $dynamicTagValue ) > strlen( $checkValue );
                }
                $break = true;
                break;

            case 'between':
                $condition = $dynamicTagValue >= $checkValue && $dynamicTagValue <= $checkValue2;
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
     * @param $dynamicTagValue
     * @param $compareType
     */
    private function parseDynamicTagValue( &$dynamicTagValue, $compareType ) {
        switch ( $compareType ) {
            case 'days':
                $dynamicTagValue = date( 'N', DynamicConditionsDate::stringToTime( $dynamicTagValue ) );
                break;

            case 'months':
                $dynamicTagValue = date( 'n', DynamicConditionsDate::stringToTime( $dynamicTagValue ) );
                break;

            case 'strtotime':
                // nobreak
            case 'date':
                $dynamicTagValue = DynamicConditionsDate::stringToTime( $dynamicTagValue );
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
    private function renderDebugInfo( $settings, $dynamicTagValue, $checkValue, $checkValue2, $conditionMets ) {
        if ( !$settings['dynamicconditions_debug'] ) {
            return;
        }

        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
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
