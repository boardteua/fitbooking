<?php

/**
 * @link              https://emagicone.com
 * @since             1.0.0
 * @package           fit_plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Fitness Booking Manager
 * Plugin URI:        https://emagicone.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.1.0
 * Author:            eMagicOne
 * Author URI:        https://emagicone.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fit-plugin
 * Domain Path:       /languages
 */


if (!defined('WPINC')) {
    die;
}
define('fit_plugin_VERSION', '1.1.0');
define('TABLE_NAME', 'bk_events');
define('FIT_BOOKING_PLUGIN_DIR', plugin_dir_path(__FILE__));

if (file_exists(FIT_BOOKING_PLUGIN_DIR . '/vendor/autoload.php')) {
    require_once FIT_BOOKING_PLUGIN_DIR . '/vendor/autoload.php';
}

use fitPlugin\fitPlugin;

/**
 * Define acf
 */

define('MY_ACF_PATH', plugin_dir_path(__FILE__) . '/src/acf/');
define('MY_ACF_URL', plugin_dir_path(__FILE__) . '/src/acf/');


function activate_fit_plugin()
{
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
    $table_name = $wpdb->get_blog_prefix() . TABLE_NAME;
    $customers_table = $table_name . 'customer';

    $sql = "CREATE TABLE {$table_name} (
            id  bigint(20) unsigned NOT NULL auto_increment,
            day DATETIME NOT NULL,
            start DATETIME NOT NULL,
            end DATETIME NOT NULL,
            color varchar(16) NOT NULL default '',
            title TEXT NOT NULL default '',
            product_id TEXT NOT NULL default '',
            trainer_id bigint(20) NOT NULL,
            room_id bigint(20) NOT NULL,
            places_pool TEXT NOT NULL default '',            
            PRIMARY KEY  (id)
            )
            {$charset_collate};";

    dbDelta($sql);

    $sql = "CREATE TABLE {$customers_table} (
            id  bigint(20) unsigned NOT NULL auto_increment,
            order_id bigint(20) NOT NULL,
            event_id bigint(20) NOT NULL,
            name TEXT NOT NULL default '',
            surname TEXT NOT NULL default '',            
            PRIMARY KEY  (id)
            )
            {$charset_collate};";

    dbDelta($sql);
}

register_activation_hook(__FILE__, 'activate_fit_plugin');


function deactivate_fit_plugin()
{
    return false;
}

register_deactivation_hook(__FILE__, 'deactivate_fit_plugin');

function runFitPlugin()
{

    $plugin = new fitPlugin();
    $plugin->run();

}

runFitPlugin();
