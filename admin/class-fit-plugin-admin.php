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
class fit_plugin_Admin
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

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook)
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
        if ('toplevel_page_fit-booking' === $hook) {
            wp_enqueue_style($this->fit_plugin, plugin_dir_url(__FILE__) . 'css/fit-plugin-admin.css', array(), $this->version, 'all');
            wp_enqueue_style($this->fit_plugin . '-fullcalendar', plugin_dir_url(__FILE__) . 'css/main.css', array(), $this->version, 'all');
            wp_enqueue_style($this->fit_plugin . '-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook)
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

        if ('toplevel_page_fit-booking' === $hook) {
            wp_enqueue_script($this->fit_plugin, plugin_dir_url(__FILE__) . 'js/main.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-fullcalendar', plugin_dir_url(__FILE__) . 'js/fit-plugin-admin.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), $this->version, false);

            wp_localize_script($this->fit_plugin, 'fit',
                array(
                    'nonce' => wp_create_nonce('fit-nonce'),
                    'rooms' => $this->get_rooms(),
                    'trainers' => $this->get_trainers(),
                )
            );
        }
    }

    /**
     * Register gym post type
     */

    private function get_rooms()
    {
        $posts = get_posts([
            'post_type' => 'gym',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        $rooms = [];

        foreach ($posts as $room) {
            $rooms[] = (object)array(
                'ID' => $room->ID,
                'post_title' => $room->post_title,
                'pool_capacity' => array(
                    get_field('place_count_rows', $room->ID),
                    get_field('place_count_cols', $room->ID))
            );
        }

        return $rooms ? $rooms : [];

    }

    /**
     * Register gym post type
     */

    /**
     * @return array
     */

    private function get_trainers()
    {
        $posts = get_posts([
            'post_type' => 'trainer',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        $trainers = [];

        foreach ($posts as $trainer) {
            $trainers[] = (object)array(
                'ID' => $trainer->ID,
                'post_title' => $trainer->post_title
            );
        }

        return $trainers ? $trainers : [];
    }

    /**
     * Register event post type
     */

    /**
     * @return void
     */
    public function trainer_post_type(): void
    {
        $labels = [
            'name' => _x('Trainer', 'Post Type General Name', 'fit-plugin'),
            'singular_name' => _x('Trainer', 'Post Type Singular Name', 'fit-plugin'),
            'menu_name' => __('Trainers', 'fit-plugin'),
            'name_admin_bar' => __('Trainer', 'fit-plugin'),
            'archives' => __('Item Archives', 'fit-plugin'),
            'attributes' => __('Item Attributes', 'fit-plugin'),
            'parent_item_colon' => __('Parent Item:', 'fit-plugin'),
            'all_items' => __('All Items', 'fit-plugin'),
            'add_new_item' => __('Add New Item', 'fit-plugin'),
            'add_new' => __('Add New', 'fit-plugin'),
            'new_item' => __('New Trainer', 'fit-plugin'),
            'edit_item' => __('Edit Trainer', 'fit-plugin'),
            'update_item' => __('Update Trainer', 'fit-plugin'),
            'view_item' => __('View Trainer', 'fit-plugin'),
            'view_items' => __('View Trainer', 'fit-plugin'),
            'search_items' => __('Search Trainer', 'fit-plugin'),
            'not_found' => __('Not found', 'fit-plugin'),
            'not_found_in_trash' => __('Not found in Trash', 'fit-plugin'),
            'featured_image' => __('Featured Image', 'fit-plugin'),
            'set_featured_image' => __('Set featured image', 'fit-plugin'),
            'remove_featured_image' => __('Remove featured image', 'fit-plugin'),
            'use_featured_image' => __('Use as featured image', 'fit-plugin'),
            'insert_into_item' => __('Insert into item', 'fit-plugin'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'fit-plugin'),
            'items_list' => __('Trainer list', 'fit-plugin'),
            'items_list_navigation' => __('Trainer list navigation', 'fit-plugin'),
            'filter_items_list' => __('Filter Trainer list', 'fit-plugin'),
        ];
        $args = [
            'label' => __('Gym', 'fit-plugin'),
            'description' => __('Gym Description', 'fit-plugin'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'feature_image'),
            'taxonomies' => [],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        ];

        register_post_type('trainer', $args);
    }

    /**
     * @return void
     */
    public function gym_post_type(): void
    {
        $labels = [
            'name' => _x('Gym', 'Post Type General Name', 'fit-plugin'),
            'singular_name' => _x('Gym', 'Post Type Singular Name', 'fit-plugin'),
            'menu_name' => __('Gyms', 'fit-plugin'),
            'name_admin_bar' => __('Gym', 'fit-plugin'),
            'archives' => __('Item Archives', 'fit-plugin'),
            'attributes' => __('Item Attributes', 'fit-plugin'),
            'parent_item_colon' => __('Parent Item:', 'fit-plugin'),
            'all_items' => __('All Items', 'fit-plugin'),
            'add_new_item' => __('Add New Item', 'fit-plugin'),
            'add_new' => __('Add New', 'fit-plugin'),
            'new_item' => __('New Gym', 'fit-plugin'),
            'edit_item' => __('Edit Gym', 'fit-plugin'),
            'update_item' => __('Update Gym', 'fit-plugin'),
            'view_item' => __('View Gym', 'fit-plugin'),
            'view_items' => __('View Gym', 'fit-plugin'),
            'search_items' => __('Search Gym', 'fit-plugin'),
            'not_found' => __('Not found', 'fit-plugin'),
            'not_found_in_trash' => __('Not found in Trash', 'fit-plugin'),
            'featured_image' => __('Featured Image', 'fit-plugin'),
            'set_featured_image' => __('Set featured image', 'fit-plugin'),
            'remove_featured_image' => __('Remove featured image', 'fit-plugin'),
            'use_featured_image' => __('Use as featured image', 'fit-plugin'),
            'insert_into_item' => __('Insert into item', 'fit-plugin'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'fit-plugin'),
            'items_list' => __('Gym list', 'fit-plugin'),
            'items_list_navigation' => __('Gym list navigation', 'fit-plugin'),
            'filter_items_list' => __('Filter Gym list', 'fit-plugin'),
        ];
        $args = [
            'label' => __('Gym', 'fit-plugin'),
            'description' => __('Gym Description', 'fit-plugin'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'feature_image'),
            'taxonomies' => [],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        ];

        register_post_type('gym', $args);
    }

    /**
     * @return void
     */
    public function event_post_type(): void
    {

        $labels = array(
            'name' => _x('Events', 'Post Type General Name', 'fit-plugin'),
            'singular_name' => _x('Event', 'Post Type Singular Name', 'fit-plugin'),
            'menu_name' => __('Events', 'fit-plugin'),
            'name_admin_bar' => __('Event', 'fit-plugin'),
            'archives' => __('Item Archives', 'fit-plugin'),
            'attributes' => __('Item Attributes', 'fit-plugin'),
            'parent_item_colon' => __('Parent Item:', 'fit-plugin'),
            'all_items' => __('All Items', 'fit-plugin'),
            'add_new_item' => __('Add New Item', 'fit-plugin'),
            'add_new' => __('Add New', 'fit-plugin'),
            'new_item' => __('New Event', 'fit-plugin'),
            'edit_item' => __('Edit Event', 'fit-plugin'),
            'update_item' => __('Update Event', 'fit-plugin'),
            'view_item' => __('View Event', 'fit-plugin'),
            'view_items' => __('View Events', 'fit-plugin'),
            'search_items' => __('Search Events', 'fit-plugin'),
            'not_found' => __('Not found', 'fit-plugin'),
            'not_found_in_trash' => __('Not found in Trash', 'fit-plugin'),
            'featured_image' => __('Featured Image', 'fit-plugin'),
            'set_featured_image' => __('Set featured image', 'fit-plugin'),
            'remove_featured_image' => __('Remove featured image', 'fit-plugin'),
            'use_featured_image' => __('Use as featured image', 'fit-plugin'),
            'insert_into_item' => __('Insert into item', 'fit-plugin'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'fit-plugin'),
            'items_list' => __('Event list', 'fit-plugin'),
            'items_list_navigation' => __('Events list navigation', 'fit-plugin'),
            'filter_items_list' => __('Filter events list', 'fit-plugin'),
        );
        $args = array(
            'label' => __('Event', 'fit-plugin'),
            'description' => __('Event Description', 'fit-plugin'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'feature_image'),
            'taxonomies' => [],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        );

        register_post_type('event', $args);

    }

    public function fit_booking_add_plugin_page()
    {
        add_menu_page(
            'Fit Booking', // page_title
            'Fit Booking', // menu_title
            'manage_options', // capability
            'fit-booking', // menu_slug
            array($this, 'fit_booking_create_admin_page'), // function
            'dashicons-schedule', // icon_url
            6 // position
        );
    }

    public function fit_booking_create_admin_page()
    {
        $this->fit_booking_options = get_option('fit_booking_option_name'); ?>

        <div class="wrap">
            <h2>FIT BOOKING</h2>
            <p>SCHEDULE A FITNESS CLASS</p>
            <?php settings_errors(); ?>

            <form method="post" class="fit-config-form" action="options.php">
                <?php
                settings_fields('fit_booking_option_group');
                do_settings_sections('fit-booking-admin');

                ?>
            </form>
            <div id='loading'>loading...</div>
            <div id='calendar'></div>
            <?php submit_button(); ?>
        </div>
    <?php }

    public function fit_booking_page_init()
    {
        register_setting(
            'fit_booking_option_group', // option_group
            'fit_booking_option_name', // option_name
            array($this, 'fit_booking_sanitize') // sanitize_callback
        );

        add_settings_section(
            'fit_booking_setting_section', // id
            'Settings', // title
            array($this, 'fit_booking_section_info'), // callback
            'fit-booking-admin' // page
        );

        add_settings_field(
            'select_room_0', // id
            'Select room', // title
            array($this, 'select_room_0_callback'), // callback
            'fit-booking-admin', // page
            'fit_booking_setting_section' // section
        );
    }

    public function fit_booking_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['select_room_0'])) {
            $sanitary_values['select_room_0'] = $input['select_room_0'];
        }

        return $sanitary_values;
    }

    public function fit_booking_section_info()
    {

    }

    public function select_room_0_callback()
    {
        ?> <select name="fit_booking_option_name[select_room_0]" id="select_room_0"> <?php
        foreach ($this->get_rooms() as $item) {
            $selected = isset($this->fit_booking_options['select_room_0']) && $this->fit_booking_options['select_room_0'] == $item->ID ? 'selected' : '';
            $capacity = implode(',', $item->pool_capacity);
            echo "<option value='$item->ID' id='room-$item->ID' data-capacity='$capacity' data-id='$item->ID' $selected> $item->post_title</option>";
        }
        ?>
    </select> <?php
    }

    /**
     * @return array
     */

    public function get_options_ajax()
    {
        check_ajax_referer('fit-nonce', 'nonce');

        $opt['trainers'] = $this->get_trainers();

        $opt['rooms'] = $this->get_rooms();

        if ($opt) {
            wp_send_json_success($opt);
        } else {
            wp_send_json_error('No opt was found');
        }
    }

    /**
     * Customize the url setting to fix incorrect asset URLs.
     */


    function my_acf_settings_url($url)
    {
        return MY_ACF_URL;
    }

    /**
     * (Optional) Hide the ACF admin menu item.
     */

    public
    function my_acf_settings_show_admin($show_admin)
    {
        return false;
    }

}
