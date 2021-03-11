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


    public function get_orders()
    {
        $return_json = array();
        $orders = $this->get_all_orders();

        if ($orders) {
            foreach ($orders as $order) {

                $event = $this->get_db_calendar_by_id($order['event_id']);

                $event_title = $event['title'];
                $event_date = $event['start'];
                $event_room = $this->get_room_by_id($event['room_id'])[0]->post_title;
                $event_trainer = $this->get_trainer_by_id($event['trainer_id'])[0]->post_title;
                $status = '';

                if ($order['cancel_reason'] !== NULL) {
                    $status .= '<span class="cancel-' . $order['cancel_reason'] . '">' . $order['cancel_reason'] . '</span>';
                }


                $row = array(
                    'event' => '<strong>' . $event_title . '</strong> <span class="event-date">Date: ' . $event_date . '</span> <span class="event-trainer">Trainer: ' . $event_trainer . '</span>',
                    'event_room' => $event_room,
                    'order_id' => $order['order_id'],
                    'name' => $order['name'],
                    'surname' => $order['surname'],
                    'email' => $order['email'],
                    'phone' => $order['phone'],
                    'place' => $order['place'],
                    'note' => $order['note'],
                    'status' => $status,
                );
                $return_json[] = $row;
            }

            echo json_encode(array('data' => $return_json));
            wp_die();
        } else {
            echo json_encode(array('data' => false));
            wp_die();
        }
    }

    protected function get_all_orders()
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bk_eventscustomer", 'ARRAY_A');
        if ($c_items === [] || $c_items === NULL) {
            return false;
        } else {
            return $c_items;
        }

    }

    protected function get_db_calendar_by_id($id)
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE id = {$id}", 'ARRAY_A');
        if ($c_items !== NULL || !empty($c_items)) {
            return $c_items[0];
        } else {
            return false;
        }
    }

    public function get_room_by_id($id)
    {
        $attr = array(
            'post_type' => 'gym',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
        );
        if (isset($id) && $id > 0) {
            $attr['include'] = [$id];
        }
        $posts = get_posts($attr);
        $rooms = [];

        foreach ($posts as $room) {


            $images = get_field('gym_gallery', $room->ID);

            if ($images !== null) {
                foreach (explode(',', $images) as $image) {
                    $img_url[] = wp_get_attachment_image_src($image, 'thumbnail')[0];
                }
            }


            $rooms[] = (object)array(
                'ID' => $room->ID,
                'post_title' => $room->post_title,
                'post_content' => $room->post_content,
                'product_id' => get_field('product_id', $room->ID),
                'product_price' => '100',
                'pool_capacity' => array(
                    get_field('place_count_rows', $room->ID),
                    get_field('place_count_cols', $room->ID)
                ),
                'room_gallery' => isset($img_url) ? $img_url : '',
            );
        }

        return $rooms ? $rooms : [];

    }

    public function get_trainer_by_id($id)
    {
        $attr = array(
            'post_type' => 'trainer',
            'post_status' => 'publish',
            'numberposts' => -1
        );
        if (isset($id) && $id > 0) {
            $attr['include'] = [$id];
        }
        $posts = get_posts($attr);
        $trainers = [];

        foreach ($posts as $trainer) {

            $img_url = array();

            $images = get_field('trainer_photos', $trainer->ID);

            if ($images !== null) {
                foreach (explode(',', $images) as $image) {
                    $img_url[] = wp_get_attachment_image_src($image, 'thumbnail')[0];
                }
            }

            $trainers[] = (object)array(
                'ID' => $trainer->ID,
                'post_title' => $trainer->post_title,
                'post_content' => $trainer->post_content,
                'trainer_photos' => $img_url
            );
        }

        return $trainers ? $trainers : [];
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
        if (function_exists('flush_pgcache')) {
            flush_pgcache();  //page cache
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
        $input = $this->process_date($input);

        $c_items = $wpdb->update("{$this->table_name}", $input, array('id' => $input['id']));

        if (!$c_items) {
            return new \WP_Error('update_error', 'Event update db error');
        }
        return $c_items;
    }

    private function process_date($event)
    {
        $event['day'] = date_format(date_create($event['start']), "Y-m-d");
        $event['start'] = date_format(date_create($event['start']), "Y-m-d H:i:s");
        $event['end'] = date_format(date_create($event['end']), "Y-m-d H:i:s");
        return $event;
    }

    protected function update_db_calendar_place($input)
    {
        global $wpdb;
        if (!array_key_exists('places_pool', $input)) {
            $input['places_pool'] = '';
        }
        $c_items = $wpdb->update("{$this->table_name}", $input, array('id' => $input['id']));

        if ($c_items !== 1) {
            return new \WP_Error('update_error', 'Event update db error');
        }

        return $c_items;
    }

    protected function set_db_calendar($input)
    {
        global $wpdb;

        $input = $this->process_date($input);
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
        return $c_items;
    }

    protected function get_db_calendar()
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$this->table_name}", 'ARRAY_A');
        return $c_items;
    }

    protected function add_event_order($input)
    {
        global $wpdb;

        error_log(serialize($input));

        $c_items = $wpdb->insert("{$wpdb->prefix}bk_eventscustomer", $input);

        error_log(serialize($c_items));
        if ($c_items !== 1) {
            return new \WP_Error('insert_error', 'Order insert db error');
        }
        return $c_items;

    }

    protected function get_event_order($order_id)
    {
        global $wpdb;
        $c_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bk_eventscustomer  WHERE order_id = {$order_id}", 'ARRAY_A');

        return $c_items;

    }

    protected function remove_event_order($order_id)
    {
        global $wpdb;
        $c_items = $wpdb->delete("{$wpdb->prefix}bk_eventscustomer", array('order_id' => $order_id));
        error_log('Order ' . $order_id . 'was removed');
        error_log(print_r($c_items));
        if ($c_items !== 1) {
            return new \WP_Error('remove_error', 'Order remove db error');
        }
        return $c_items;

    }

    protected function set_event_order_status($order_id, $cancel_reason, $cancelled_at)
    {
        global $wpdb;

        $c_items = $wpdb->udpdate("{$wpdb->prefix}bk_eventscustomer",
            array('cancel_reason' => $cancel_reason, 'cancelled_at' => $cancelled_at),
            array('ID' => $order_id)
        );

        if ($c_items !== 1) {
            return new \WP_Error('update_error', 'Order status update db error');
        }
        return $c_items;
    }


}