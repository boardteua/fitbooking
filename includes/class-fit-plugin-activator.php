<?php

/**
 * Fired during plugin activation
 *
 * @link       https://emagicone.com
 * @since      1.0.0
 *
 * @package    fit_plugin
 * @subpackage fit_plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    fit_plugin
 * @subpackage fit_plugin/includes
 * @author     eMagicOne <support@emagicone.com>
 */
class fit_plugin_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
        $table_name = $wpdb->get_blog_prefix() . TABLE_NAME;

        $sql = "CREATE TABLE {$table_name} (
            id  bigint(20) unsigned NOT NULL auto_increment,
            day DATETIME NOT NULL,
            start DATETIME NOT NULL,
            end DATETIME NOT NULL,
            color varchar(16) NOT NULL default '',
            title TEXT NOT NULL default '',
            trainer_id bigint(20) NOT NULL,
            room_id bigint(20) NOT NULL,
            places_pool TEXT NOT NULL default '',            
            PRIMARY KEY  (id)
            )
            {$charset_collate};";

        dbDelta($sql);
    }

}
