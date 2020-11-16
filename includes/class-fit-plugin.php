<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://emagicone.com
 * @since      1.0.0
 *
 * @package    fit_plugin
 * @subpackage fit_plugin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    fit_plugin
 * @subpackage fit_plugin/includes
 * @author     eMagicOne <support@emagicone.com>
 */
class fit_plugin
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      fit_plugin_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $fit_plugin The string used to uniquely identify this plugin.
     */
    protected $fit_plugin;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */

    protected $table;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('fit_plugin_VERSION')) {
            $this->version = fit_plugin_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        if (defined('TABLE_NAME')) {
            $this->table = TABLE_NAME;
        } else {
            $this->table = 'bk_events';
        }

        $this->fit_plugin = 'fit-plugin';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - fit_plugin_Loader. Orchestrates the hooks of the plugin.
     * - fit_plugin_i18n. Defines internationalization functionality.
     * - fit_plugin_Admin. Defines all hooks for the admin area.
     * - fit_plugin_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class implements custom template loading for wp
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-fit-template.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-fit-plugin-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-fit-plugin-i18n.php';


        /**
         * The class responsible for defining shopify api functions
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-fit-plugin-shopify.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-fit-plugin-admin.php';

        /**
         * The class responsible for defining table CRUD actions
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-fit-plugin-table.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-fit-plugin-public.php';

        /**
         *  Template functions
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-fit-table.php';

        // Include the ACF plugin.
        if (!class_exists('ACF')) {
            include_once(MY_ACF_PATH . 'acf.php');
        }


        $this->loader = new fit_plugin_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the fit_plugin_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new fit_plugin_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new fit_plugin_Admin($this->get_fit_plugin(), $this->get_version());
        $plugin_table = new fit_plugin_Table($this->get_fit_plugin(), $this->get_version(), $this->get_table());
        $plugin_table = new fit_plugin_shopify($this->get_fit_plugin(), $this->get_version(), $this->get_table());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_action('init', $plugin_admin, 'event_post_type');
        $this->loader->add_action('init', $plugin_admin, 'gym_post_type');
        $this->loader->add_action('init', $plugin_admin, 'trainer_post_type');


        if (!class_exists('acf')) {
            $this->loader->add_filter('acf/settings/url', $plugin_admin, 'my_acf_settings_url');
            $this->loader->add_filter('acf/settings/show_admin', $plugin_admin, 'my_acf_settings_show_admin');
        }

        $this->loader->add_action('admin_menu', $plugin_admin, 'fit_booking_add_plugin_page');
        $this->loader->add_action('admin_init', $plugin_admin, 'fit_booking_page_init');

        /* --- ajax actions --- */

        $this->loader->add_action('wp_ajax_table_actions', $plugin_table, 'table_actions');
        $this->loader->add_action('wp_ajax_get_options', $plugin_admin, 'get_options_ajax');
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_fit_plugin()
    {
        return $this->fit_plugin;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    public function get_table()
    {
        global $wpdb;

        return $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$this->table}'") === $wpdb->prefix . $this->table ? $wpdb->prefix . $this->table :
            new WP_Error('table_exist', 'db table exist');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new fit_plugin_Public(
            $this->get_fit_plugin(),
            $this->get_version(),
            $this->get_table(),
            new Fit_Booking_Template_Loader()
        );

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        /*----*/
        $this->loader->add_shortcode('fittable', $plugin_public, 'booking_shortcode');

        /*----*/
        $this->loader->add_action('wp_ajax_get_event', $plugin_public, 'get_event_ajax');
        $this->loader->add_action('wp_ajax_nopriv_get_event', $plugin_public, 'get_event_ajax');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    fit_plugin_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

}
