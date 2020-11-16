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

?>
<?php
$template = new Fit_Booking_Template_Loader();
$template->get_template_part('filters');
?>
<div class="container">
    <div class="row fit-table-wrp">
        <?php
        foreach (group_by('room_id', $data->rooms) as $room => $days) {
            $template->set_template_data(array(
                'room' => get_rooms($room),
                'days' => $days
            ))->get_template_part('room');
        }
        ?>
    </div>
</div>
