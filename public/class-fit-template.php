<?php
if (!class_exists('Gamajo_Template_Loader')) {
    require_once plugin_dir_path(dirname(__FILE__)) . '/includes/class-fit-template-loader.php';
}

/**
 * Template loader for Meal Planner.
 *
 * Only need to specify class properties here.
 *
 * @package Meal_Planner
 * @author  Gary Jones
 */
class Fit_Booking_Template_Loader extends Gamajo_Template_Loader
{
    /**
     * Prefix for filter names.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $filter_prefix = 'fit_booking';

    /**
     * Directory name where custom templates for this plugin should be found in the theme.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $theme_template_directory = 'fitbooking';

    /**
     * Reference to the root directory path of this plugin.
     *
     * Can either be a defined constant, or a relative reference from where the subclass lives.
     *
     * In this case, `MEAL_PLANNER_PLUGIN_DIR` would be defined in the root plugin file as:
     *
     * ~~~
     * define( 'MEAL_PLANNER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
     * ~~~
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $plugin_directory = FIT_BOOKING_PLUGIN_DIR;

    /**
     * Directory name where templates are found in this plugin.
     *
     * Can either be a defined constant, or a relative reference from where the subclass lives.
     *
     * e.g. 'templates' or 'includes/templates', etc.
     *
     * @since 1.1.0
     *
     * @var string
     */
    protected $plugin_template_directory = 'public/templates';
}