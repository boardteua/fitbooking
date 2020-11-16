<?php

if (!function_exists('get_rooms')) {
    function get_rooms($id)
    {
        $attr = array(
            'post_type' => 'gym',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby'     => 'date',
            'order'       => 'ASC',
        );
        if (isset($id) && $id > 0) {
            $attr['include'] = [$id];
        }
        $posts = get_posts($attr);
        $rooms = [];
        foreach ($posts as $room) {
            $rooms[] = (object)array(
                'ID' => $room->ID,
                'post_title' => $room->post_title,
                'pool_capacity' => array(
                    get_field('place_count_rows', $room->ID),
                    get_field('place_count_cols', $room->ID)
                ),
                'room_gallery' => get_field('gym_gallery', $room->ID),
            );
        }

        return $rooms ? $rooms : [];

    }
}
if (!function_exists('get_trainers')) {
    function get_trainers($id)
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
            $trainers[] = (object)array(
                'ID' => $trainer->ID,
                'post_title' => $trainer->post_title
            );
        }

        return $trainers ? $trainers : [];
    }
}
if (!function_exists('group_by')) {
    function group_by($key, $data)
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
}