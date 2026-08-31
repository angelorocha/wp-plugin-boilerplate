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
final class PluginInit {
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
	 * @var string
	 * @since 1.0.0
	 */
	private static string $file_version = '20260820';

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
	 * @see   get_plugin_prefix() to get plugin text domain;
	 * @see   get_plugin_path() to get absolut plugin path;
	 * @see   get_plugin_url() to get plugin url;
	 * @see   ajax_check_referer() to validate ajax calls
	 * @since 1.0.0
	 */
	protected function __construct() {

		if ( empty( self::$global_params ) ) {
			self::$global_params = [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::$nonce ),
				'home_url' => home_url(),
			];
		}

		/** Define plugin absolute path */
		if ( empty( self::$plugin_path ) ) {
			self::$plugin_path = BOILERPLATE_DIR_PATH;
		}

		/** Define plugin absolute path */
		if ( empty( self::$plugin_url ) ) {
			self::$plugin_url = BOILERPLATE_DIR_URL;
		}

		/** Load plugin text domain */
		add_action( 'plugins_loaded', [ $this, 'load_plugin_text_domain' ] );

		/** Load admin global styles and script */
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_style_and_scripts' ] );

		/** Load public global styles and script */
		add_action( 'wp_enqueue_scripts', [ $this, 'load_public_style_and_scripts' ] );

		/** Flush plugin rewrite rules */
		add_action( 'init', [ $this, 'flush_rewrite_rules' ] );

		/** Execute plugin updates */
		add_action( 'init', [ $this, 'plugin_update_action' ] );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return self A single instance of this class.
	 * @since 1.0.0
	 */
	public static function get_instance(): self {
		/** If the single instance hasn't been set, set it now. */
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Validate ajax calls
	 *
	 * @param bool $is_admin_request check if is admin request.
	 * @since 1.0.0
	 */
	public static function ajax_check_referer( bool $is_admin_request = true ): void {
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
	public function load_plugin_text_domain(): void {
		/*
		 * Sorry, but I prefer using a standard translation package for my country's language; I don't like unfair warnings.
		 */
		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound
		load_plugin_textdomain( self::$prefix, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Load admin styles and scripts.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load_admin_style_and_scripts(): void {
		if ( $this->assets_exists( 'admin.min.css', false, true ) ) {
			wp_enqueue_style( self::$prefix . self::$admin_css_file, self::$plugin_url . 'admin/assets/css/admin.min.css', [], self::$file_version );
		}
		if ( $this->assets_exists( 'admin.min.js', true, true ) ) {
			wp_enqueue_script( self::$prefix . self::$admin_js_file, self::$plugin_url . 'admin/assets/js/admin.min.js', [ 'jquery' ], self::$file_version, true );
			wp_localize_script( self::$prefix . self::$admin_js_file, self::$global_params_object_name, self::$global_params );
		}
	}

	/**
	 * Load styles and scripts.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load_public_style_and_scripts(): void {
		if ( $this->assets_exists( 'public.min.css', false, false ) ) {
			wp_enqueue_style( self::$prefix . self::$public_css_file, self::$plugin_url . 'public/assets/css/public.min.css', [], self::$file_version );
		}
		if ( $this->assets_exists( 'public.min.js', true, false ) ) {
			wp_enqueue_script( self::$prefix . self::$public_js_file, self::$plugin_url . 'public/assets/js/public.min.js', [ 'jquery' ], self::$file_version, true );
			wp_localize_script( self::$prefix . self::$public_js_file, self::$global_params_object_name, self::$global_params );
		}
	}

	/**
	 * Verify if asset exist.
	 *
	 * @param string $file     asset file path.
	 * @param bool   $is_js    check if is js file.
	 * @param bool   $is_admin check if is admin assets.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function assets_exists( string $file, bool $is_js, bool $is_admin ): bool {
		$path        = $is_admin ? 'admin/assets/' : 'public/assets/';
		$asset_file  = $is_js ? 'js/' : 'css/';
		$assets_path = self::$plugin_path . $path . $asset_file;

		return file_exists( $assets_path . $file );
	}

	/**
	 * Return plugin absolute path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_plugin_path(): string {
		return self::$plugin_path;
	}

	/**
	 * Return plugin url.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_plugin_url(): string {
		return self::$plugin_url;
	}

	/**
	 * Get plugin prefix to internationalization.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_plugin_prefix(): string {
		return self::$prefix;
	}

	/**
	 * Flush WordPress rewrite rules.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function flush_rewrite_rules(): void {
		if ( is_admin() && get_option( 'saibba_flush_rewrite' ) ) {
			flush_rewrite_rules();
			delete_option( 'saibba_flush_rewrite' );
		}
	}

	/**
	 * Plugin activate hook.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function plugin_activate_action(): void {
		if ( ! get_option( 'saibba_flush_rewrite' ) ) {
			add_option( 'saibba_flush_rewrite', true );
		}
		if ( ! get_option( 'boilerplate_plugin_version' ) ) {
			add_option( 'boilerplate_plugin_version', self::get_plugin_version() );
		}
	}

	/**
	 * Plugin deactivate hook.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function plugin_deactivate_action(): void {
		// TODO: implements deatctivate actions here.
	}

	/**
	 * Execute plugin db updates or other actions.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function plugin_update_action(): void {
		// TODO: implements update actions here.
	}

	/**
	 * Return plugin version.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_plugin_version(): string {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		return get_plugin_data( BOILERPLATE_MAIN_FILE )['Version'];
	}
}
