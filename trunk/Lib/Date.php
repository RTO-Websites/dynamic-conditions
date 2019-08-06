<?php namespace DynamicConditions\Lib;

/**
 * Class Date
 * @package DynamicConditions\Lib
 */
class Date {

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
     * @return int
     */
    public static function stringToTime( $string = '' ) {
        $timestamp = $string;
        $strToTime = strtotime( $string );
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
        $monthList = self::getMonths();

        // translate monthlist by wordpress-lang
        foreach ( $monthList as &$month ) {
            $month = __( $month );
        }

        return $monthList;
    }

    /**
     * Loops all months an return in a list
     *
     * @return array
     */
    private static function getMonths() {
        $monthList = [];
        for ( $i = 1; $i <= 12; ++$i ) {
            $monthList[$i] = strftime( '%B', mktime( 0, 0, 0, $i, 1 ) );
        }

        return $monthList;
    }

    /**
     * Get a list of days (monday, tuesday,...) in current language
     *
     * @return array
     */
    public static function getDaysTranslated() {
        $dayList = self::getDays();

        // translate by wordpress-lang
        foreach ( $dayList as &$day ) {
            $day = __( $day );
        }

        return $dayList;
    }

    /**
     * Loops all days an return in a list
     *
     * @return array
     */
    private static function getDays() {
        $dayList = [];
        $year = date( 'o', time() );
        $week = date( 'W', time() );
        for ( $i = 1; $i <= 7; $i++ ) {
            $time = strtotime( $year . 'W' . $week . $i );
            $dayList[$i] = strftime( "%A", $time );
        }

        return $dayList;
    }
}
