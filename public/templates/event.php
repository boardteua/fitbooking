<?php
/**
 * Template to render events.
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
<li data-event-id="<?= $data->event['id'] ?>" data-trainer-id="<?= get_trainers($data->event['trainer_id'])[0]->ID ?>"
    class="event-wrp list-group-item">
    <strong class="eventr-date mr-1"><?= date_format(date_create($data->event['start']), "H:i") ?></strong>
    <i clas="event-title"><?= $data->event['title'] ?></i>
    <span class="event-trainer text-black-50 ml-auto"><?= get_trainers($data->event['trainer_id'])[0]->post_title ?></span>
</li>
