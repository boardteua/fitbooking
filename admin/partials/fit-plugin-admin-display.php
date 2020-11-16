<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://emagicone.com
 * @since      1.0.0
 *
 * @package    fit_plugin
 * @subpackage fit_plugin/admin/partials
 */

class fitPluginAdmin
{

    public function fit_booking_add_plugin_page()
    {
        add_menu_page(
            'FIT BOOKING', // page_title
            'FIT BOOKING', // menu_title
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

            <form method="post" action="options.php">
                <?php
                settings_fields('fit_booking_option_group');
                do_settings_sections('fit-booking-admin');
                submit_button();
                ?>
            </form>
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
            'hello_0', // id
            'hello', // title
            array($this, 'hello_0_callback'), // callback
            'fit-booking-admin', // page
            'fit_booking_setting_section' // section
        );
    }

    public function fit_booking_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['hello_0'])) {
            $sanitary_values['hello_0'] = sanitize_text_field($input['hello_0']);
        }

        return $sanitary_values;
    }

    public function fit_booking_section_info()
    {

    }

    public function hello_0_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="fit_booking_option_name[hello_0]" id="hello_0" value="%s">',
            isset($this->fit_booking_options['hello_0']) ? esc_attr($this->fit_booking_options['hello_0']) : ''
        );
    }

}

if (is_admin())
    $fit_booking = new FITBOOKING();

/* 
 * Retrieve this value with:
 * $fit_booking_options = get_option( 'fit_booking_option_name' ); // Array of All Options
 * $hello_0 = $fit_booking_options['hello_0']; // hello
 */
