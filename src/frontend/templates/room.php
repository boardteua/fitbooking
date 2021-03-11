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

use fitPlugin\frontend\templates;
use fitplugin\frontend\utils\utils;

$template = new templates();
$options = get_option('fit_booking_option_name');
$utils = utils::getInstance();

?>
<?php if ($data->room[0]->ID !== 0): ?>
    <div class="room_wrp w-100 hidden"
         data-id="<?= $data->room[0]->ID ?>"
         data-shop="<?= array_key_exists('api_shop_domain_0', $options) ? $options['api_shop_domain_0'] : '#' ?>">
        <h3><?= esc_attr($data->room[0]->post_title) ?></h3>
        <?php if ($data->days): ?>
            <div class="card-group owl-carousel owl-theme">
                <?php
                foreach ($utils->group_by('day', $data->days) as $day => $events) {
                    $template->set_template_data(array(
                        'day' => $day,
                        'events' => $events
                    ))->get_template_part('events');
                }
                ?>
            </div>
        <?php else: ?>
            <div class="card-group no-item">
                No Events in Gym
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>