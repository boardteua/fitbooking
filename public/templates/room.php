<?php
/**
 * Template to render room.
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
$template = new Fit_Booking_Template_Loader();
?>

<div class="room_wrp w-100 hidden" data-id="<?= $data->room[0]->ID ?>">
    <h3><?= esc_attr($data->room[0]->post_title) ?></h3>
    <div class="card-group owl-carousel owl-theme">
        <?php
        foreach (group_by('day', $data->days) as $day => $events) {
            $template->set_template_data(array(
                'day' => $day,
                'events' => $events
            ))->get_template_part('events');
        }
        ?>
    </div>
</div>