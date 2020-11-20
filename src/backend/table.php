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

namespace fitPlugin\backend;

class table
{


    /**
     *  Events list table
     *
     * @param string $version plugin events db table
     * @access private
     * @since 1.0.0
     */

    protected $table_name;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $fit_plugin The name of the plugin.
     * @param string $version The version of this plugin.
     * @param string $table_name Plugin db table name.
     * @since    1.0.0
     */


    public function __construct($table_name)
    {

        $this->table_name = $table_name;

    }

    /**
     * @return string
     */
    public function getFitPlugin(): string
    {
        return $this->fit_plugin;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table_name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }


    public function table_actions()
    {
        require_once('utils.php');
        check_ajax_referer('fit-nonce', 'nonce');

        $flag = '';
        $room_id = false;
        $event = array();
        if (isset($_POST['flag'])) {
            $flag = filter_var(strip_tags($_POST['flag']), FILTER_SANITIZE_STRING);
        }
        if (isset($_POST['room_id'])) {
            $room_id = filter_var(strip_tags($_POST['room_id']), FILTER_SANITIZE_STRING);
        }
        if (isset($_POST['ti_data'])) {
            $event = $_POST['ti_data'];
            if (array_key_exists('places_pool', $event) && is_array($event['places_pool'])) {
                $event['places_pool'] = $event['places_pool'][0];
            }
        }

        if (isset($event['id']) && $flag == 'del') {
            $this->delete_db_calendar($event['id']);
        }
        if (isset($event) && $flag == 'update') {
            $this->update_db_calendar($event);
        }

        if (isset($event) && $flag == 'update_place') {
            $this->update_db_calendar_place($event);
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

        if ($room_id) {
            $input_arrays = $this->get_db_calendar_by_room($room_id);
        } else {
            $input_arrays = $this->get_db_calendar();
        }

        ob_clean();
        echo json_encode($input_arrays);
        wp_die();
    }

    protected function delete_db_calendar($id)
    {
        global $wpdb;

        $c_items = $wpdb->delete("{$this->table_name}", array('id' => $id));
        if ($c_items === 0) {
            return new \WP_Error('delete_error', 'Event delete db error');
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
            return new \WP_Error('update_error', 'Event update db error');
        }
        return $c_items;
    }

    protected function update_db_calendar_place($input)
    {
        global $wpdb;
        if (!array_key_exists('places_pool', $input)) {
            $input['places_pool'] = '';
        }
        $c_items = $wpdb->update("{$this->table_name}", $input, array('id' => $input['id']));

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
            return new \WP_Error('insert_error', 'Event insert db error');
        }
        return $c_items;
    }

    protected function get_db_calendar_by_room($id)
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE room_id = {$id}", 'ARRAY_A');
        return $c_items[0];
    }

    protected function get_db_calendar()
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name}", 'ARRAY_A');
        return $c_items;
    }

    protected function get_db_calendar_by_id($id)
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE id = {$id}", 'ARRAY_A');
        return $c_items[0];
    }


}