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

use fitPlugin\frontend\templates;
use fitplugin\frontend\utils\utils;

$template = new templates();
$utils = utils::getInstance();
?>
<li data-event-id="<?= $data->event['id'] ?>"
    data-trainer-id="<?= $utils->get_trainers($data->event['trainer_id'])[0]->ID ?>"
    data-product="<?= $data->event['product_id'] ?>"
    data-price="<?= $utils->get_price($data->event['product_id']) ?>"
    class="event-wrp list-group-item">
    <strong class="eventr-date mr-2"><?= date_format(date_create($data->event['start']), "H:i") ?></strong>
    <span class="event-title"><?= $data->event['title'] ?></span>
    <span class="event-trainer text-black-50 ml-auto"><?= $utils->get_trainers($data->event['trainer_id'])[0]->post_title ?> <span
                class="mini-ava ml-2"><img
                    src="<?= $utils->get_trainers($data->event['trainer_id'])[0]->trainer_photos[0] ?>"/></span> </span>
</li>