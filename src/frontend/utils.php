<?php


namespace fitplugin\frontend\utils;


use fitPlugin\backend\shopifyApi;

final class utils
{
    private static $instance = null;

    private $client;

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
        $this->client = new shopifyApi();
    }

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): utils
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function get_rooms($id)
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


            $images = get_field('gym_gallery',$room->ID);

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
                'room_gallery' => $img_url,
            );
        }

        return $rooms ? $rooms : [];

    }

    public function get_trainers($id)
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

    public function group_by($key, $data)
    {
        $result = array();

        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }

    public function get_currency(): string
    {
        return $this->client->get_store_info()->money_format;
    }

    public function get_price($id): string
    {
        return $this->client->get_price($id);
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }
}