<?php
/**
 * Plugin boilerplate to generate initial plugin structure.
 *
 * @package   plugin-boilerplate
 * @copyright @TODO
 * @author    @TODO
 * @wordpress-plugin
 * Plugin Name: @TODO
 * Plugin URI: @TODO
 * Description: @TODO
 * Version: 1.0.0
 * Author: @TODO
 * Author URI: @TODO
 * License: GNU General Public License v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: plugin-text-domain
 * Domain Path: /lang
 * GitHub Plugin URI: @TODO
 */

namespace BoilerplatePluginName;

use BoilerplatePluginName\Admin\PluginInit;

/** Prevent direct access */
if ( ! function_exists( 'add_action' ) ) {
	header( 'HTTP/1.0 401 Unauthorized' );
	exit;
}

/** Execute autoload */
if ( ! defined( 'BOILERPLATE_DIR_PATH' ) ) {
	define( 'BOILERPLATE_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BOILERPLATE_DIR_URL' ) ) {
	define( 'BOILERPLATE_DIR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BOILERPLATE_MAIN_FILE' ) ) {
	define( 'BOILERPLATE_MAIN_FILE', __FILE__ );
}

require_once BOILERPLATE_DIR_PATH . 'autoload.php';
boilerplate_autoload( BOILERPLATE_DIR_PATH );

/** Init plugin */
add_action( 'plugins_loaded', [ PluginInit::class, 'get_instance' ] );

/** Plugin activate action */
register_activation_hook( __FILE__, [ PluginInit::class, 'plugin_activate_action' ] );

/** Plugin uninstall action */
register_deactivation_hook( __FILE__, [ PluginInit::class, 'plugin_deactivate_action' ] );
