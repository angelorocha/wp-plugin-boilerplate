<?php
/**
 * Plugin boilerplate to generate initial plugin structure.
 *
 * @package plugin-boilerplate
 * @copyright @TODO
 * @author @TODO
 * @wordpress-plugin
 * Plugin Name: @TODO
 * Plugin URI: @TODO
 * Description: @TODO
 * Version: 1.0.0
 * Author: @TODO
 * Author URI: @TODO
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
if ( ! defined( 'PLUGIN_BOILERPLATE_DIR_PATH' ) ) {
	define( 'PLUGIN_BOILERPLATE_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'PLUGIN_BOILERPLATE_DIR_URL' ) ) {
	define( 'PLUGIN_BOILERPLATE_DIR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PLUGIN_BOILERPLATE_MAIN_FILE' ) ) {
	define( 'PLUGIN_BOILERPLATE_MAIN_FILE', __FILE__ );
}

$plugin_path = PLUGIN_BOILERPLATE_DIR_PATH;
require_once $plugin_path . 'autoload.php';
boilerplate_autoload( $plugin_path );

/** Init plugin */
add_action( 'plugins_loaded', [ PluginInit::class, 'getInstance' ], 0 );

/** Plugin activate action */
register_activation_hook( PLUGIN_BOILERPLATE_DIR_PATH, [ PluginInit::class, 'pluginActivateAction' ] );

/** Plugin uninstall action */
register_deactivation_hook( PLUGIN_BOILERPLATE_DIR_PATH, [ PluginInit::class, 'pluginUninstallAction' ] );
