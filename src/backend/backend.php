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

namespace fitPlugin\backend;

class backend
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
        if ('toplevel_page_fit_booking' === $hook) {
            wp_enqueue_style($this->fit_plugin, plugin_dir_url(__FILE__) . 'css/fit-plugin-admin.css', array(), $this->version, 'all');
            wp_enqueue_style($this->fit_plugin . '-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->fit_plugin . '-datatable', '//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->fit_plugin . '-datatable-resp', 'https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap4.min.css', array(), $this->version, 'all');
            //wp_enqueue_style($this->fit_plugin . '-datatable-btst', 'https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->fit_plugin . '-datatable-btst-gr', 'https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.bootstrap4.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->fit_plugin . '-fullcalendar', plugin_dir_url(__FILE__) . 'css/main.css', array(), $this->version, 'all');

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

        if ('toplevel_page_fit_booking' === $hook) {
            wp_enqueue_script($this->fit_plugin, plugin_dir_url(__FILE__) . 'js/main.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-datatable', '//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-datatable-resp', 'https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-datatable-resp-btstr', 'https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap4.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-datatable-btstr', 'https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-datatable-grp', 'https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-fullcalendar', plugin_dir_url(__FILE__) . 'js/fit-plugin-admin.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->fit_plugin . '-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), $this->version, false);

            wp_localize_script($this->fit_plugin, 'fit',
                array(
                    'nonce' => wp_create_nonce('fit-nonce'),
                    'rooms' => $this->get_rooms(),
                    'trainers' => $this->get_trainers(),
                    'products' => $this->product_list()
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
                'product_id' => get_field('product_id', $room->ID),
                'pool_capacity' => array(
                    get_field('place_count_rows', $room->ID),
                    get_field('place_count_cols', $room->ID))
            );
        }

        return $rooms ? $rooms : [];

    }

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

    private function product_list()
    {
        $products = new shopifyApi();
        $pool = array();
        if ($products->get_products()) {
            foreach ($products->get_products() as $product) {
                foreach ($product->variants as $variant) {
                    $pool[$variant->id] = $product->title . ' ' . $variant->title;
                }
            }
        }

        return $pool;
    }

    /**
     * Register gym post type
     */

    public function add_local_field_groups()
    {
        acf_add_local_field_group(array(
            'key' => 'group_5fa429da56db6',
            'title' => 'Gym',
            'fields' => array(
                array(
                    'key' => 'field_5fa42aa85217e',
                    'label' => 'Place Rows',
                    'name' => 'place_count_rows',
                    'type' => 'range',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 2,
                    'min' => 0,
                    'max' => 100,
                    'step' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_5faabdc7918e7',
                    'label' => 'Place Cols',
                    'name' => 'place_count_cols',
                    'type' => 'range',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 10,
                    'min' => 0,
                    'max' => 100,
                    'step' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_5fa42ae25217f',
                    'label' => 'Gym Gallery',
                    'name' => 'gym_gallery',
                    'type' => 'photo_gallery',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'fields[gym_gallery' => array(
                        'edit_modal' => 'Default',
                        'images_limit' => '',
                    ),
                    'edit_modal' => 'Default',
                ),
                array(
                    'key' => 'field_5fa42ae25219g',
                    'label' => 'Product Name (For dev)',
                    'name' => 'product_id',
                    'type' => 'select',
                    'choices' => $this->product_list()
                )
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'gym',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

        acf_add_local_field_group(array(
            'key' => 'group_5face56920742',
            'title' => 'Trainer',
            'fields' => array(
                array(
                    'key' => 'field_5fb2dbc1edc8e',
                    'label' => 'Trainer Photos',
                    'name' => 'trainer_photos',
                    'type' => 'photo_gallery',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'fields[' => array(
                        'edit_modal' => 'Default',
                        'images_limit' => '',
                    ),
                    'edit_modal' => 'Default',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'trainer',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }

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

        //register_post_type('event', $args);

    }

    public function fit_booking_add_plugin_main_page()
    {
        add_menu_page(
            'Fit Booking', // page_title
            'Fit Booking', // menu_title
            'manage_options', // capability
            'fit_booking', // menu_slug
            array($this, 'fit_booking_create_admin_page'), // function
            'dashicons-schedule', // icon_url
            6 // position
        );
    }

    public function fit_booking_add_plugin_setting_page()
    {
        add_submenu_page(
            'fit_booking',
            'Fit Booking Setting', // page_title
            'Setting', // menu_title
            'manage_options', // capability
            'fit_booking_setting', // menu_slug
            array($this, 'fit_booking_create_admin_setting_page'), // function
            1
        );
    }

    public function fit_booking_create_admin_page()
    {
        $this->fit_booking_options = get_option('fit_booking_option_name'); ?>
        <div class="wrap">

            <form method="post" class="fit-config-form" action="options.php">
                <?php settings_errors(); ?>
                <?php
                settings_fields('fit_booking_option_group');
                do_settings_sections('fit-booking-admin');
                ?>
            </form>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link nav-calendar active" data-toggle="tab" href="#view-calendar">Calendar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#orders">Customer list</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active container" id="view-calendar">
                    <div id='calendar'></div>
                </div>
                <div class="tab-pane container py-3" id="orders">
                    <table id="event_orders" class="" style="width:100%">
                        <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Event Room</th>
                            <th>Order Id</th>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>eMail</th>
                            <th>Phone</th>
                            <th>Place</th>
                            <th>Note</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Event Title</th>
                            <th>Event Room</th>
                            <th>Order Id</th>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>eMail</th>
                            <th>Phone</th>
                            <th>Place</th>
                            <th>Note</th>
                        </tr>
                        </tfoot>
                    </table>


                </div>
            </div>
        </div>

    <?php }

    public function fit_booking_create_admin_setting_page()
    {
        $this->fit_booking_options = get_option('fit_booking_option_name'); ?>

        <div class="wrap">
            <?php settings_errors(); ?>
            <form method="post" class="fit-config-form" action="options.php">
                <?php
                settings_fields('fit_booking_option_group');
                do_settings_sections('fit-booking-setting');
                submit_button();
                ?>
            </form>
        </div>
        <?php

    }

    public function fit_booking_page_init()
    {
        register_setting(
            'fit_booking_option_group', // option_group
            'fit_booking_option_name', // option_name
            array($this, 'fit_booking_sanitize') // sanitize_callback
        );
////
        add_settings_section(
            'fit_booking_calendar_section', // id
            'Booking Calendar', // title
            array($this, 'fit_booking_section_info'), // callback
            'fit-booking-admin' // page
        );

        add_settings_field(
            'select_room_0', // id
            'Select room', // title
            array($this, 'select_room_0_callback'), // callback
            'fit-booking-admin', // page
            'fit_booking_calendar_section' // section
        );

////

        add_settings_section(
            'fit_booking_setting_section', // id
            'Booking Setting', // title
            array($this, 'fit_booking_section_setting'), // callback
            'fit-booking-setting' // page
        );

        add_settings_field(
            'api_key_0', // id
            'API key', // title
            array($this, 'api_key_callback'), // callback
            'fit-booking-setting', // page
            'fit_booking_setting_section' // section
        );

        add_settings_field(
            'api_pass_0', // id
            'API password', // title
            array($this, 'api_key_password_callback'), // callback
            'fit-booking-setting', // page
            'fit_booking_setting_section' // section
        );

        add_settings_field(
            'api_shop_domain_0', // id
            'Shop Domain', // title
            array($this, 'api_key_domain_callback'), // callback
            'fit-booking-setting', // page
            'fit_booking_setting_section' // section
        );

        add_settings_field(
            'api_shared_secret_0', // id
            'Shared Secret', // title
            array($this, 'api_key_secret_callback'), // callback
            'fit-booking-setting', // page
            'fit_booking_setting_section' // section
        );

        add_settings_field(
            'acollection_id_0', // id
            'Shopify collection ID', // title
            array($this, 'collection_id_callback'), // callback
            'fit-booking-setting', // page
            'fit_booking_setting_section' // section
        );

    }

    public function fit_booking_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['select_room_0'])) {
            $sanitary_values['select_room_0'] = $input['select_room_0'];
        }

        if (isset($input['api_key_0'])) {
            $sanitary_values['api_key_0'] = $input['api_key_0'];
        }

        if (isset($input['api_pass_0'])) {
            $sanitary_values['api_pass_0'] = $input['api_pass_0'];
        }

        if (isset($input['api_shop_domain_0'])) {
            $sanitary_values['api_shop_domain_0'] = $input['api_shop_domain_0'];
        }

        if (isset($input['api_shared_secret_0'])) {
            $sanitary_values['api_shared_secret_0'] = $input['api_shared_secret_0'];
        }

        if (isset($input['collection_id_0'])) {
            $sanitary_values['collection_id_0'] = $input['collection_id_0'];
        }
        return $sanitary_values;
    }

    public function fit_booking_section_info()
    {

    }

    public function fit_booking_section_setting()
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

    public function api_key_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="fit_booking_option_name[api_key_0]" id="api_key_0" value="%s">',
            isset($this->fit_booking_options['api_key_0']) ? esc_attr($this->fit_booking_options['api_key_0']) : ''
        );
    }

    public function api_key_password_callback()
    {
        printf(
            '<input class="regular-text" type="password" name="fit_booking_option_name[api_pass_0]" id="api_pass_0" value="%s">',
            isset($this->fit_booking_options['api_pass_0']) ? esc_attr($this->fit_booking_options['api_pass_0']) : ''
        );
    }

    public function api_key_domain_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="fit_booking_option_name[api_shop_domain_0]" id="api_shop_domain_0" value="%s">',
            isset($this->fit_booking_options['api_shop_domain_0']) ? esc_attr($this->fit_booking_options['api_shop_domain_0']) : ''
        );
    }

    public function api_key_secret_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="fit_booking_option_name[api_shared_secret_0]" id="api_shared_secret_0" value="%s">',
            isset($this->fit_booking_options['api_shared_secret_0']) ? esc_attr($this->fit_booking_options['api_shared_secret_0']) : ''
        );
    }

    public function collection_id_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="fit_booking_option_name[collection_id_0]" id="collection_id_0" value="%s">',
            isset($this->fit_booking_options['collection_id_0']) ? esc_attr($this->fit_booking_options['collection_id_0']) : ''
        );
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

    public function my_acf_settings_url($url)
    {
        return MY_ACF_URL;
    }

    /**
     * (Optional) Hide the ACF admin menu item.
     */

    public function my_acf_settings_show_admin($show_admin)
    {
        return false;
    }

}
