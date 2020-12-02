<?php
/**
 * Template to render rooms.
 *
 * @link              https://emagicone.com
 * @since             1.0.0
 * @package           fit_plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Fitness Booking Manager
 * Plugin URI:        https://emagicone.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            eMagicOne
 * Author URI:        https://emagicone.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fit-plugin
 * Domain Path:       /languages
 */

use fitplugin\frontend\utils\utils;

$utils = utils::getInstance();

?>

<div class="container room-filters">
    <div class="row">
        <form class="d-flex w-100 justify-content-md-start justify-content-center">
            <div class="form-group mr-2 w-100">
                <label for="filter_by_room">Select Gym</label>
                <select class="form-control" id="filter_by_room" >
                    <?php foreach ($utils->get_rooms(0) as $room) { ?>
                        <option data-id="<?= $room->ID ?>" id="room-<?= $room->ID ?>"><?= $room->post_title ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group mr-2 w-100">
                <label for="filter_by_trainer">Select Trainer</label>
                <select class="form-control" id="filter_by_trainer" >
                    <option data-id="0">All</option>
                    <?php foreach ($utils->get_trainers(0) as $trainer) { ?>
                        <option data-id="<?= $trainer->ID ?>"
                                id="trainer-<?= $trainer->ID ?>"><?= $trainer->post_title ?></option>
                    <?php } ?>
                </select>
            </div>
    </div>
</div>
