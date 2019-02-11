<?php namespace Pub;

class DynamicConditionsDate {

    /**
     * Filter date-output from date_i18n() to return always a timestamp
     *
     * @param string $j Formatted date string.
     * @param string $req_format Format to display the date.
     * @param int $i Unix timestamp.
     * @param bool $gmt Whether to convert to GMT for time. Default false.
     * @return int Unix timestamp
     */
    public function filterDateI18n( $j, $req_format, $i, $gmt ) {
        return $i;
    }

    /**
     * Filters the date of a post to return a timestamp
     *
     * @param string|bool $the_time The formatted date or false if no post is found.
     * @param string $d PHP date format. Defaults to value specified in
     *                               'date_format' option.
     * @param WP_Post|null $post WP_Post object or null if no post is found.
     *
     * @return mixed
     */
    public function filterPostDate( $the_time, $d, $post ) {
        if ( empty( $d ) ) {
            return $the_time;
        }
        $date = \DateTime::createFromFormat( $d, $the_time );
        if ( empty( $date ) ) {
            return $the_time;
        }

        return $date->getTimestamp();
    }

    /**
     * Convert string to timestamp or return string if it´s already a timestamp
     *
     * @param $string
     * @return false|int
     */
    public static function stringToTime( $string = '' ) {
        $timestamp = $string;
        $strToTime = strtotime( $string );
        if ( !empty( $strToTime ) ) {
            $timestamp = $strToTime;
        }

        return $timestamp;
    }

    /**
     * Untranslate a date-string to english date
     *
     * @param string $needle
     * @param null $setLocale
     * @return mixed|string
     */
    public static function unTranslateDate( $needle = '', $setLocale = null ) {
        if ( empty( $setLocale ) ) {
            $setLocale = get_locale();
        }
        $currentLocale = setlocale( LC_TIME, 0 );

        $year = date( 'o', time() );
        $week = date( 'W', time() );

        $englishMonths = [];
        $englishDays = [];
        $translatedMonths = [];
        $translatedDays = [];

        setlocale( LC_TIME, $setLocale );

        // get in translated lang
        for ( $i = 1; $i <= 12; ++$i ) {
            $translatedMonths[$i] = strftime( '%B', mktime( 0, 0, 0, $i, 1 ) );
        }

        for ( $i = 1; $i <= 7; $i++ ) {
            $time = strtotime( $year . 'W' . $week . $i );
            $translatedDays[$i] = strftime( "%A", $time );
        }

        setlocale( LC_TIME, $currentLocale );

        // get in english
        for ( $i = 1; $i <= 12; ++$i ) {
            $englishMonths[$i] = date( 'F', mktime( 0, 0, 0, $i, 1 ) );
        }

        for ( $i = 1; $i <= 7; $i++ ) {
            $time = strtotime( $year . 'W' . $week . $i );
            $englishDays[$i] = date( "l", $time );
        }

        $needle = str_ireplace( $translatedDays, $englishDays, $needle );
        $needle = str_ireplace( $translatedMonths, $englishMonths, $needle );

        return $needle;
    }


    /**
     * Get a list of months (january, february,...)
     *
     * @return array
     */
    public static function getMonths() {
        $currentLocale = setlocale( LC_TIME, 0 );
        setlocale( LC_TIME, get_locale() );
        $monthList = [];
        for ( $i = 1; $i <= 12; ++$i ) {
            $monthList[$i] = strftime( '%B', mktime( 0, 0, 0, $i, 1 ) );
        }
        setlocale( LC_TIME, $currentLocale );

        return $monthList;
    }

    /**
     * Get a list of days (monday, tuesday,...)
     *
     * @return array
     */
    public static function getDays() {
        $currentLocale = setlocale( LC_TIME, 0 );
        setlocale( LC_TIME, get_locale() );
        $dayList = [];
        $year = date( 'o', time() );
        $week = date( 'W', time() );
        for ( $i = 1; $i <= 7; $i++ ) {
            $time = strtotime( $year . 'W' . $week . $i );
            $dayList[$i] = strftime( "%A", $time );
        }
        setlocale( LC_TIME, $currentLocale );

        return $dayList;
    }
}
