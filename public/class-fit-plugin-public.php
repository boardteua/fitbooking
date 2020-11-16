<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://emagicone.com
 * @since      1.0.0
 *
 * @package    fit_plugin
 * @subpackage fit_plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    fit_plugin
 * @subpackage fit_plugin/public
 * @author     eMagicOne <support@emagicone.com>
 */
class fit_plugin_Public
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
     * @var      string $version The current version of this plugin.
     */
    private $version;


    /**
     *  Events list table
     *
     * @param string $version plugin events db table
     * @access private
     * @since 1.0.0
     */

    private $table_name;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $fit_plugin The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */

    private $template;

    public function __construct($fit_plugin, $version, $table_name, $template)
    {

        $this->fit_plugin = $fit_plugin;
        $this->version = $version;
        $this->table_name = $table_name;
        $this->template = $template;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in fit_plugin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The fit_plugin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->fit_plugin, plugin_dir_url(__FILE__) . 'css/fit-plugin-public.css', array(), $this->version, 'all');
        wp_enqueue_style($this->fit_plugin . '-owl', plugin_dir_url(__FILE__) . 'css/owl.carousel.css', array(), $this->version, 'all');
        wp_enqueue_style($this->fit_plugin . '-owl-theme', plugin_dir_url(__FILE__) . 'css/owl.theme.green.css', array(), $this->version, 'all');


    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in fit_plugin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The fit_plugin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->fit_plugin, plugin_dir_url(__FILE__) . 'js/fit-plugin-public.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->fit_plugin . '-owl', plugin_dir_url(__FILE__) . 'js/owl.carousel.min.js', array('jquery'), $this->version, false);
        wp_localize_script($this->fit_plugin, 'fit',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('fit-nonce'),
                'rooms' => get_rooms(0),
                'trainers' => get_trainers(0),
            )
        );
    }

    /**
     * @return string
     */

    public function booking_shortcode()
    {
        do_action('before_shorcode');
        $this->template->set_template_data(array(
            'rooms' => $this->get_db_calendar()
        ))->get_template_part('rooms');
        do_action('after_shortcode');
    }

    protected function get_db_calendar()
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY start", 'ARRAY_A');
        return $c_items;
    }

    /**
     * @return string
     */
    public function get_event_ajax()
    {
        $id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
        $event = [];
        if (isset($id)) {
            $event = $this->get_db_event($id);
        }
        wp_send_json_success($event);
    }

    protected function get_db_event($id)
    {
        global $wpdb;


        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE id = {$id} ORDER BY start", 'ARRAY_A');
        return $c_items[0];
    }

}
