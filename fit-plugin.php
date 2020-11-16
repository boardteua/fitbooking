<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://emagicone.com
 * @since             1.0.0
 * @package           fit_plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Fitness Booking Manager
 * Plugin URI:        https://emagicone.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            eMagicOne or Your Company
 * Author URI:        https://emagicone.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fit-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('fit_plugin_VERSION', '1.0.0');
define('TABLE_NAME', 'bk_events');
define('FIT_BOOKING_PLUGIN_DIR', plugin_dir_path(__FILE__));
/**
 * Define acf
 */

define('MY_ACF_PATH', plugin_dir_path(__FILE__) . '/includes/acf/');
define('MY_ACF_URL', plugin_dir_path(__FILE__) . '/includes/acf/');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fit-plugin-activator.php
 */
function activate_fit_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-fit-plugin-activator.php';
    fit_plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fit-plugin-deactivator.php
 */
function deactivate_fit_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-fit-plugin-deactivator.php';
    fit_plugin_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_fit_plugin');
register_deactivation_hook(__FILE__, 'deactivate_fit_plugin');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-fit-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fit_plugin()
{

    $plugin = new fit_plugin();
    $plugin->run();

}

run_fit_plugin();
