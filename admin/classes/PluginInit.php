<?php
/**
 * Plugin initialization class.
 *
 * @package plugin-boilerplate
 */

namespace BoilerplatePluginName\Admin;

/** Prevent direct access */
if ( ! function_exists( 'add_action' ) ) {
	header( 'HTTP/1.0 401 Unauthorized' );
	exit;
}

/**
 * Class to init all basic plugin setup.
 *
 * @since 1.0.0
 */
class PluginInit {
	/**
	 * Instance of this class.
	 *
	 * @var self|null
	 * @since 1.0.0
	 */
	protected static ?self $instance = null;

	/**
	 * Prefix plugin
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $prefix = 'plugin-name';

	/**
	 * Define the plugin script object name
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $global_params_object_name = 'global_params';

	/**
	 * Define assets version
	 *
	 * @var int
	 * @since 1.0.0
	 */
	private static int $file_version = 20260820;

	/**
	 * Define admin css handle
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $admin_css_file = '-admin-main-css';

	/**
	 * Define public css handle
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $public_css_file = '-main-css';

	/**
	 * Define admin js handle
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $admin_js_file = '-admin-main-js';

	/**
	 * Define public js handle
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $public_js_file = '-main-js';

	/**
	 * Define plugin absolute path
	 *
	 * @var string|null
	 * @since 1.0.0
	 */
	public static ?string $plugin_path = '';

	/**
	 * Define plugin url
	 *
	 * @var string|null
	 * @since 1.0.0
	 */
	public static ?string $plugin_url = '';

	/**
	 * Prefix plugin
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private static array $global_params = [];

	/**
	 * Secure nonce for ajax calls
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $nonce = 'boilerplate-nonce';

	/**
	 * Initialize the plugin
	 *
	 * @see getPluginPrefix() to get plugin text domain;
	 * @see getPluginDirPath() to get absolut plugin path;
	 * @see getPluginDirUrl() to get plugin url;
	 * @see ajaxCheckReferer() to validate ajax calls
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( empty( self::$global_params ) ) {
			self::$global_params = [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::$nonce ),
				'home_url' => home_url(),
			];
		}

		/** Define plugin absolute path */
		if ( empty( self::$plugin_path ) ) {
			self::$plugin_path = PLUGIN_BOILERPLATE_DIR_PATH;
		}

		/** Define plugin absolute path */
		if ( empty( self::$plugin_url ) ) {
			self::$plugin_url = PLUGIN_BOILERPLATE_DIR_URL;
		}

		/** Load plugin text domain */
		add_action( 'plugins_loaded', [ $this, 'loadPluginTextDomain' ] );

		/** Load admin global styles and script */
		add_action( 'admin_enqueue_scripts', [ $this, 'loadAdminStylesAndScripts' ] );

		/** Load public global styles and script */
		add_action( 'wp_enqueue_scripts', [ $this, 'loadPublicStylesAndScripts' ] );

		/** Flush plugin rewrite rules */
		add_action( 'init', [ $this, 'flushRewriteRules' ], 20 );

		/** Execute plugin updates */
		add_action( 'init', [ $this, 'pluginUpdateActions' ] );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return self A single instance of this class.
	 * @since 1.0.0
	 */
	public static function getInstance(): self {
		/** If the single instance hasn't been set, set it now. */
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Validate ajax calls
	 *
	 * @since 1.0.0
	 */
	public static function ajaxCheckReferer( bool $is_admin_request = true ): void {
		if ( $is_admin_request ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				header( 'HTTP/1.0 403 Forbidden' );
				exit;
			}
		}
		
		check_ajax_referer( self::$nonce, 'nonce' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function loadPluginTextDomain(): void {
		load_plugin_textdomain( self::$prefix, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Load admin styles and scripts
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function loadAdminStylesAndScripts(): void {
		if ( self::assetExist( 'main.css', false, true ) ) {
			wp_enqueue_style( self::$prefix . self::$admin_css_file, self::$plugin_url . 'admin/assets/css/main.css', [], self::$file_version );
		}
		if ( self::assetExist( 'main.js', true, true ) ) {
			wp_enqueue_script( self::$prefix . self::$admin_js_file, self::$plugin_url . 'admin/assets/js/main.js', [ 'jquery' ], self::$file_version, true );
			wp_localize_script( self::$prefix . self::$admin_js_file, self::$global_params_object_name, self::$global_params );
		}
	}

	/**
	 * Load styles and scripts
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function loadPublicStylesAndScripts(): void {
		if ( self::assetExist( 'main.css', false, false ) ) {
			wp_enqueue_style( self::$prefix . self::$public_css_file, self::$plugin_url . 'public/assets/css/main.css', [], self::$file_version );
		}
		if ( self::assetExist( 'main.js', true, false ) ) {
			wp_enqueue_script( self::$prefix . self::$public_js_file, self::$plugin_url . 'public/assets/js/main.js', [ 'jquery' ], self::$file_version, true );
			wp_localize_script( self::$prefix . self::$public_js_file, self::$global_params_object_name, self::$global_params );
		}
	}

	/**
	 * Verify if asset exist
	 *
	 * @param string $file asset file path.
	 * @param bool   $is_js check if is js file.
	 * @param bool   $is_admin check if is admin assets.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function assetExist( string $file, bool $is_js, bool $is_admin ): bool {
		$path        = $is_admin ? 'admin/assets/' : 'public/assets/';
		$asset_file  = $is_js ? 'js/' : 'css/';
		$assets_path = self::$plugin_path . $path . $asset_file;

		return file_exists( $assets_path . $file );
	}

	/**
	 * Return plugin absolute path
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function getPluginDirPath(): string {
		return self::$plugin_path;
	}

	/**
	 * Return plugin url
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function getPluginDirUrl(): string {
		return self::$plugin_url;
	}

	/**
	 * Get plugin prefix to internationalization
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function getPluginPrefix(): string {
		return self::$prefix;
	}

	/**
	 * Flush WordPress rewrite rules
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function flushRewriteRules(): void {
		if ( is_admin() && get_option( 'saibba_flush_rewrite' ) ) {
			flush_rewrite_rules();
			delete_option( 'saibba_flush_rewrite' );
		}
	}

	/**
	 * Plugin activate hook
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function pluginActivateAction(): void {
		if ( ! get_option( 'saibba_flush_rewrite' ) ) {
			add_option( 'saibba_flush_rewrite', true );
		}
		if ( ! get_option( 'boilerplate_plugin_version' ) ) {
			add_option( 'boilerplate_plugin_version', self::getPluginVersion() );
		}
	}

	/**
	 * Plugin deactivate hook
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function pluginUninstallAction(): void {
		// do something...
	}

	/**
	 * Execute plugin db updates or other actions
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function pluginUpdateActions(): void {
		// do something...
	}

	/**
	 * Return plugin version
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function getPluginVersion(): string {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		return get_plugin_data( PLUGIN_BOILERPLATE_MAIN_FILE )['Version'];
	}
}
