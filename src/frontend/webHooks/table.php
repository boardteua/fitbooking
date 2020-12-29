<?php


namespace fitPlugin\frontend\webHooks;

class table extends \fitPlugin\backend\table
{

    public function orders_fulfilled($data)
    {
        if (!$data)
            return false;

        $attr = $this->get_attributes($data);

        $processed_pool = $this->to_string(array_unique(array_merge($attr['order_pool'], $attr['current_pool']), SORT_NUMERIC));

        $res = $this->update_db_calendar_place(array(
            'id' => $data['note_attributes'][0]['value'],
            'places_pool' => $processed_pool
        ));

        $order['order_id'] = $data['id'];
        $order['event_id'] = $data['note_attributes'][0]['value'];
        $order['name'] = $data['customer']['first_name'];
        $order['surname'] = $data['customer']['last_name'];
        $order['email'] = $data['customer']['email'];
        $order['place'] = $data['note_attributes'][5]['value'];
        $order['phone'] = $data['customer']['phone'];
        $order['note'] = $data['note_attributes'][7]['value'];

        if ($this->get_event_order($order['order_id']) === []) {
            $this->add_event_order($order);
        }

        if (is_a($res, 'WP_Error')) {
            throw new \Exception();
        } else {
            error_log('pool processed: ' . $processed_pool);
        }
    }

    private function get_attributes($data): array
    {
        $current_pool = array();
        $new_pool = array();
        $attr = $data['note_attributes'];

        error_log('Current pool: ' . serialize($data));

        $event = $this->get_db_calendar_by_id($attr[0]['value']);
        if (is_a($event, 'WP_Error')) {
            throw new \Exception();
        }

        if (array_key_exists('places_pool', $event)) {
            $current_pool = $this->to_array($event['places_pool']);
        }

        if (array_key_exists(5, $attr)) {
            $new_pool = $this->to_array($attr[5]['value']);
        }

        error_log('Current pool: ' . serialize($current_pool));
        error_log('Order pool: ' . serialize($new_pool));

        return array('current_pool' => $current_pool, 'order_pool' => $new_pool);
    }

    private function to_array($input): array
    {
        return strpos($input, ',') !== false ?
            explode(',', $input) : [$input]; //explode — Split a string by a string
    }

    private function to_string($input): string
    {
        return count($input) > 1 ?
            implode(',', $input) : $input[0];  //implode — Join array elements with a string
    }

    public function orders_cancelled($data)
    {
        if (!$data)
            return false;

        $attr = $this->get_attributes($data);


        $order['event_id'] = $data['note_attributes'][0]['value'];
        $order['order_id'] = $data['id'];

        if ($this->get_event_order($order['order_id']) === $data['id']) {
            $this->remove_event_order($order['order_id']);
        }


        error_log('array dif: ' . serialize(array_diff($attr['order_pool'], $attr['current_pool'])));

        $processed_pool = $this->to_string(array_diff($attr['order_pool'], $attr['current_pool']));

        $args = array(
            'id' => $data['note_attributes'][0]['value'],
            'places_pool' => $processed_pool
        );
        $res = $this->update_db_calendar_place($args);
        if (is_a($res, 'WP_Error')) {
            throw new \Exception();
        }

    }

    public function order_updated($data)
    {

    }
}