<?php


/**
 * The full calendar functionality of the plugin.
 *
 * Calendar CRUD operations
 *
 * @package    fit_plugin
 * @subpackage fit_plugin/admin
 * @author     eMagicOne <support@emagicone.com>
 */
class fit_plugin_Table
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
     * @param string $table_name Plugin db table name.
     * @since    1.0.0
     */


    public function __construct($fit_plugin, $version, $table_name)
    {

        $this->fit_plugin = $fit_plugin;
        $this->version = $version;
        $this->table_name = $table_name;

    }

    public function table_actions()
    {
        require_once('utils.php');
        check_ajax_referer('fit-nonce', 'nonce');

        $flag = filter_var(strip_tags($_POST['flag']), FILTER_SANITIZE_STRING);
        $room_id = filter_var(strip_tags($_POST['room_id']), FILTER_SANITIZE_STRING);
        $event = $_POST['ti_data'];

        if (isset($event['id']) && $flag == 'del') {
            $this->delete_db_calendar($event['id']);
        }
        if (isset($event) && $flag == 'update') {
            $this->update_db_calendar($event);
        }
        if (isset($_POST['start']) && isset($_POST['end'])) {
            $event['start'] = filter_var(strip_tags($_POST['start']), FILTER_SANITIZE_STRING);
            $event['end'] = filter_var(strip_tags($_POST['end']), FILTER_SANITIZE_STRING);
        }
        if (!isset($event['start']) && !isset($event['end']) && !isset($flag)) {
            wp_send_json_error("Please provide a date range.");
        }

        if (isset($event) && $flag === 'add') {
            $this->set_db_calendar($event);
        }


        // Parse the start/end parameters.
        // These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
        // Since no timezone will be present, they will parsed as UTC.

        //$range_start = date_format(date_create($event['start']), "Y-m-d H:i");
        //$range_start = parseDateTime($event['start']);
        // $range_end = parseDateTime($event['end']);
        //$range_end = date_format(date_create($event['end']), "Y-m-d H:i");

        // Parse the timezone parameter if it is present.
        $timezone = null;

        if (isset($event['timezone'])) {
            $timezone = new DateTimeZone($event['timezone']);
        }

        //$item = $this->get_db_calendar();
        if ($room_id) {

            $input_arrays = $this->get_db_calendar_by_room($room_id);
        } else {
            $input_arrays = $this->get_db_calendar();
        }

        // Accumulate an output array of event data arrays.
        $output_arrays = array();

//        foreach ($input_arrays as $array) {
//
//            // Convert the input array into a useful Event object
//
//            $events = new Event($array, $timezone);
//            // If the event is in-bounds, add it to the output
//            if ($events->isWithinDayRange($event['start'], $event['end'])) {
//                $output_arrays[] = $events->toArray();
//            }
//        }

        // Send JSON to the client.
        ob_clean();
        // echo json_encode($output_arrays);
        echo json_encode($input_arrays);
        wp_die();
//        wp_send_json_success($input_arrays);
    }

    protected function delete_db_calendar($id)
    {
        global $wpdb;

        $c_items = $wpdb->delete("{$this->table_name}", array('id' => $id));
        if ($c_items === 0) {
            return new WP_Error('delete_error', 'Event delete db error');
        }

        return $c_items;
    }

    protected function update_db_calendar($input)
    {
        global $wpdb;
        $input['day'] = date_format(date_create($input['start']), "Y-m-d");
        $input['start'] = date_format(date_create($input['start']), "Y-m-d H:i:s");
        $input['end'] = date_format(date_create($input['end']), "Y-m-d H:i:s");

        $c_items = $wpdb->update("{$this->table_name}", $input, array('id' => $input['id']));

        if (!$c_items) {
            return new WP_Error('update_error', 'Event update db error');
        }
        return $c_items;
    }

    protected function set_db_calendar($input)
    {
        global $wpdb;

        $input['day'] = date_format(date_create($input['start']), "Y-m-d");
        $input['start'] = date_format(date_create($input['start']), "Y-m-d H:i:s");
        $input['end'] = date_format(date_create($input['end']), "Y-m-d H:i:s");

        $c_items = $wpdb->insert("{$this->table_name}", $input);
        if ($c_items !== 1) {
            return new WP_Error('insert_error', 'Event insert db error');
        }
        return $c_items;
    }

    protected function get_db_calendar_by_room($id)
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE room_id = {$id}", 'ARRAY_A');
        return $c_items;
    }

    protected function get_db_calendar()
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name}", 'ARRAY_A');
        return $c_items;
    }

}