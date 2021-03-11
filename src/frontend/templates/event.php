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

if (isset($utils->get_trainers($data->event['trainer_id'])[0]->trainer_photos[0])) {
    $t_photo = "<img src=\"" . $utils->get_trainers($data->event['trainer_id'])[0]->trainer_photos[0] . "\" />";
} else {
    $t_photo = '<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13 26C20.1797 26 26 20.1797 26 13C26 5.82031 20.1797 0 13 0C5.82031 0 0 5.82031 0 13C0 20.1797 5.82031 26 13 26ZM13.25 5H7V6.9884V7.58063C8.97949 10.4599 10.3459 12.2083 11.6643 13.868H7.34088C7.22583 13.868 7.11206 13.862 7 13.8503V14.2903V15.8387V21H9V15.8387H13.2241C14.2888 17.2015 15.4586 18.7579 17 21H20C18.1949 18.7227 16.7922 16.9436 15.485 15.2631C16.9816 14.4377 18 12.8094 18 10.9355V9.9032C18 7.19525 15.8734 5 13.25 5ZM14.1328 13.5153C15.2389 12.9531 16 11.778 16 10.4194C16 8.51471 14.5042 6.97064 12.6591 6.97064H9.18695L9.19629 6.98413L9.20807 7.00134C9.22241 7.0224 9.23639 7.04346 9.25 7.06451C11.1978 9.66644 12.673 11.618 14.1328 13.5153Z" fill="black"/>
                    </svg>';
}
?>
<li data-event-id="<?= $data->event['id'] ?>"
    data-trainer-id="<?= $utils->get_trainers($data->event['trainer_id'])[0]->ID ?>"
    data-product="<?= $data->event['product_id'] ?>"
    data-price="<?= $utils->get_price($data->event['product_id']) ?>"
    class="event-wrp list-group-item">
    <strong class="eventr-date mr-2"><?= date_format(date_create($data->event['start']), "H:i") ?></strong>
    <span class="event-title"><?= $data->event['title'] ?></span>
    <span class="event-trainer text-black-50 ml-auto"><?= $utils->get_trainers($data->event['trainer_id'])[0]->post_title ?> <span
                class="mini-ava ml-2"><?php echo $t_photo ?></span> </span>
</li>