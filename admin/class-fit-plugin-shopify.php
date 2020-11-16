<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://emagicone.com
 * @since      1.0.0
 *
 * @package    fit_plugin
 * @subpackage fit_plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    fit_plugin
 * @subpackage fit_plugin/admin
 * @author     eMagicOne <support@emagicone.com>
 */
class fit_plugin_shopify
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $fit_plugin The ID of this plugin.
     */
    private $fit_plugin;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.git add .
     */
    private $version;

    /**
     * @since 1.0.0
     * @access private
     * @var string $fit_booking_options plugin options;
     *
     */

    private $fit_booking_options;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $fit_plugin The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */

    public function __construct($fit_plugin, $version)
    {

        $this->fit_plugin = $fit_plugin;
        $this->version = $version;

    }
}