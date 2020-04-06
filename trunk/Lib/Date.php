<?php

namespace DynamicConditions\Lib;

// If this file is called directly, abort.
use WP_Post;

if ( !defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Class Date
 * @package DynamicConditions\Lib
 */
class Date {

    /**
     * Filter date-output from date_i18n() to return always a timestamp
     *
     * @param string $formatedDate Formatted date string.
     * @param string $reqFormat Format to display the date.
     * @param int $unixTimestamp Unix timestamp.
     * @param bool $gmt Whether to convert to GMT for time. Default false.
     * @return int Unix timestamp
     */
    public function filterDateI18n( $formatedDate, $reqFormat, $unixTimestamp, $gmt ) {
        return $unixTimestamp;
    }

    /**
     * Filters the date of a post to return a timestamp
     *
     * @param string|bool $theTime The formatted date or false if no post is found.
     * @param string $dateFormat PHP date format. Defaults to value specified in
     *                               'date_format' option.
     * @param WP_Post|null $post WP_Post object or null if no post is found.
     *
     * @return mixed
     */
    public function filterPostDate( $theTime, $dateFormat, $post ) {
        if ( empty( $dateFormat ) ) {
            $dateFormat = get_option( 'date_format' );
        }

        $date = \DateTime::createFromFormat( $dateFormat, self::unTranslateDate( $theTime ) );

        if ( empty( $date)) {
            $date = \DateTime::createFromFormat( $dateFormat,  $theTime );
        }

        if ( empty( $date ) ) {
            return $theTime;
        }

        return $date->getTimestamp();
    }

    /**
     * Convert string to timestamp or return string if it´s already a timestamp
     *
     * @param $string
     * @return int
     */
    public static function stringToTime( $string = '' ) {
        $timestamp = $string;
        $strToTime = strtotime( $string, time() );
        if ( !empty( $strToTime ) && !self::isTimestamp( $timestamp ) ) {
            $timestamp = $strToTime;
        }

        return intval( $timestamp );
    }

    /**
     * @param string $string
     * @return bool
     */
    public static function isTimestamp( $string ) {
        if ( !is_numeric( $string ) ) {
            return false;
        }
        try {
            new \DateTime( '@' . $string );
        } catch ( \Exception $e ) {
            return false;
        }
        return true;
    }

    /**
     * Untranslate a date-string to english date
     *
     * @param string $needle
     * @param null $setLocale
     * @return mixed|string
     */
    public static function unTranslateDate( $needle = '', $setLocale = null ) {
        // get in translated lang
        $translatedMonths = self::getMonthsTranslated();
        $translatedDays = self::getDaysTranslated();

        // get in english
        $englishMonths = self::getMonths();
        $englishDays = self::getDays();

        // replace translated days/months with english ones
        $needle = str_ireplace( $translatedDays, $englishDays, $needle );
        $needle = str_ireplace( $translatedMonths, $englishMonths, $needle );

        return $needle;
    }

    /**
     * Get a list of months (january, february,...) in current language
     *
     * @return array
     */
    public static function getMonthsTranslated() {
        $monthList = [];
        // translate monthlist by wordpress-lang
        $monthList[1] = __( 'January' );
        $monthList[2] = __( 'February' );
        $monthList[3] = __( 'March' );
        $monthList[4] = __( 'April' );
        $monthList[5] = __( 'May' );
        $monthList[6] = __( 'June' );
        $monthList[7] = __( 'July' );
        $monthList[8] = __( 'August' );
        $monthList[9] = __( 'September' );
        $monthList[10] = __( 'October' );
        $monthList[11] = __( 'November' );
        $monthList[12] = __( 'December' );

        return $monthList;
    }

    /**
     * Get a list of months (january, february,...)
     *
     * @return array
     */
    private static function getMonths() {
        $monthList = [];
        $monthList[1] = 'January';
        $monthList[2] = 'February';
        $monthList[3] = 'March';
        $monthList[4] = 'April';
        $monthList[5] = 'May';
        $monthList[6] = 'June';
        $monthList[7] = 'July';
        $monthList[8] = 'August';
        $monthList[9] = 'September';
        $monthList[10] = 'October';
        $monthList[11] = 'November';
        $monthList[12] = 'December';

        return $monthList;
    }

    /**
     * Get a list of days (monday, tuesday,...) in current language
     *
     * @return array
     */
    public static function getDaysTranslated() {
        $dayList = [];

        // translate by wordpress-lang
        $dayList[1] = __( 'Monday' );
        $dayList[2] = __( 'Tuesday' );
        $dayList[3] = __( 'Wednesday' );
        $dayList[4] = __( 'Thursday' );
        $dayList[5] = __( 'Friday' );
        $dayList[6] = __( 'Saturday' );
        $dayList[7] = __( 'Sunday' );

        return $dayList;
    }

    /**
     * Get a list of days (monday, tuesday,...)
     *
     * @return array
     */
    private static function getDays() {
        $dayList = [];
        $dayList[1] = 'Monday';
        $dayList[2] = 'Tuesday';
        $dayList[3] = 'Wednesday';
        $dayList[4] = 'Thursday';
        $dayList[5] = 'Friday';
        $dayList[6] = 'Saturday';
        $dayList[7] = 'Sunday';

        return $dayList;
    }

    /**
     * Sets a local
     * Fix issue with too long locales returned by setLocale(LC_ALL, 0)
     *
     * @param $locale
     */
    public static function setLocale( $locale ) {
        $localeSettings = explode( ";", $locale );

        foreach ( $localeSettings as $localeSetting ) {
            if ( strpos( $localeSetting, "=" ) !== false ) {
                $categorylocale = explode( "=", $localeSetting );
                $category = $categorylocale[0];
                $locale = $categorylocale[1];
            } else {
                $category = LC_ALL;
                $locale = $localeSetting;
            }

            if ( is_string( $category ) && defined( $category ) ) {
                $category = constant( $category );
            }

            if ( !is_integer( $category ) ) {
                continue;
            }

            setlocale( $category, $locale );
        }
    }
}