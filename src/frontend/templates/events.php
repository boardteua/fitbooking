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

use fitplugin\frontend\templates;

$template = new templates();

$today = date("ymd") == date_format(date_create($data->day), "ymd") ? 'today' : '';

?>
<div data-date="<?= esc_attr(date_format(date_create($data->day), "ymd")) ?>" class="card item <?= $today ?>" >
    <div class="card-body">
        <h3 class="card-title"><?= esc_attr(date_format(date_create($data->day), "(M, d) l")) ?></h3>
    </div>
    <ul class="list-group list-group-flush">
        <?php
        foreach ($data->events as $event) {
            $template->set_template_data(array('event' => $event))->get_template_part('event');
        }
        ?>
    </ul>
</div>
