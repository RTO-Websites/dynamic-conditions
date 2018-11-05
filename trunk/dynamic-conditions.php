<?php

use Lib\DynamicConditions;
use Lib\DynamicConditionsActivator;
use Lib\DynamicConditionsDeactivator;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.rto.de
 * @since             1.0.0
 * @package           DynamicConditions
 *
 * @wordpress-plugin
 * Plugin Name:       DynamicConditions
 * Plugin URI:        https://github.com/RTO-Websites/dynamic-conditions
 * Description:       Activates conditions for dynamic tags to show/hides a widget.
 * Version:           1.0.0
 * Author:            RTO GmbH
 * Author URI:        https://www.rto.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dynamic-conditions
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for auto loading classes.
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/DynamicConditionsActivator.php
 */
register_activation_hook( __FILE__, array( DynamicConditionsActivator::class, 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/DynamicConditionsDeactivator.php
 */
register_deactivation_hook( __FILE__, array( DynamicConditionsDeactivator::class, 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
DynamicConditions::run();