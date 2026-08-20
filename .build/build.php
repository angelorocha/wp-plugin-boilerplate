<?php
/**
 * Build script and local test environment
 */

$action     = $argv[1] ?? 'build';
$build_dir  = __DIR__;
$is_windows = 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) );
$current_plugin_name = basename(dirname(__FILE__ , 2));

/**
 * Function to open a URL in the default browser (Cross-Platform)
 */
function open_browser( string $url ) {
	$os = strtoupper( PHP_OS );

	if ( 'WIN' === substr( $os, 0, 3 ) ) {
		pclose( popen( "start \"\" \"{$url}\"", "r" ) );
	} elseif ( 'DARWIN' === $os ) {
		exec( "open \"{$url}\" > /dev/null 2>&1 &" );
	} else {
		exec( "xdg-open \"{$url}\" > /dev/null 2>&1 &" );
	}
}

if ( 'startplugin' === $action ) {
	echo "1. Generating plugin test package...\n";

	if ( $is_windows ) {
		system( "powershell -ExecutionPolicy Bypass -File \"{$build_dir}\\build.ps1\"" );
	} else {
		system( "bash \"{$build_dir}/build.sh\"" );
	}

	$compose_file = $build_dir . DIRECTORY_SEPARATOR . 'docker-compose.yml';

	if ( ! file_exists( $compose_file ) ) {
		echo "\nERROR: The docker-compose.yml file was not found in: {$compose_file}\n";
		exit( 1 );
	}

	$cleanup = function () use ( $compose_file, $build_dir ) {
		echo "\n\n5. Removing containers and the temporary test database...\n";
		system( "docker compose -f \"{$compose_file}\" down -v" );
		echo "Environment successfully cleaned!\n";
		exit( 0 );
	};

	if ( function_exists( 'sapi_windows_set_ctrl_handler' ) ) {
		sapi_windows_set_ctrl_handler( $cleanup );
	} elseif ( function_exists( 'pcntl_async_signals' ) ) {
		pcntl_async_signals( true );
		pcntl_signal( SIGINT, $cleanup );
		pcntl_signal( SIGTERM, $cleanup );
	}

	echo "\n2. Starting Docker containers...\n";
	putenv("PLUGIN_NAME={$current_plugin_name}");
	system( "docker compose -f \"{$compose_file}\" up -d" );

	echo "\n3. Waiting for the database to start...\n";
	sleep( 12 );

	echo "\n4. Automatically installing WordPress (Login: root | Password: root)...\n";

	$wp_cli_cmd = 'docker exec ' . $current_plugin_name . '_wp_test bash -c "'
		. 'curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && '
		. 'chmod +x wp-cli.phar && '
		. './wp-cli.phar core install --allow-root '
		. '--url=\'http://localhost:8181\' '
		. '--title=\'Test Environment\' '
		. '--admin_user=\'root\' '
		. '--admin_password=\'root\' '
		. '--admin_email=\'admin@example.com\' && '
		. './wp-cli.phar plugin activate '.$current_plugin_name.' --allow-root && '
		. './wp-cli.phar plugin install plugin-check --activate --allow-root"';

	system( $wp_cli_cmd );

	$admin_url = 'http://localhost:8181/wp-admin';

	echo "\n------------------------------------------------------------\n";
	echo "All set!\n";
	echo "URL: {$admin_url}\n";
	echo "Administrator User: root\n";
	echo "Password            root\n";
	echo "------------------------------------------------------------\n";

	echo "Starting {$admin_url} in your browser\n";
	open_browser( $admin_url );

	echo "\nPress Ctrl+C in this terminal at any time to stop and delete the containers.\n\n";

	system( "docker compose -f \"{$compose_file}\" logs -f" );

	$cleanup();
}

// Default build execution
if ( $is_windows ) {
	system( "powershell -ExecutionPolicy Bypass -File \"{$build_dir}\\build.ps1\"" );
} else {
	system( "bash \"{$build_dir}/build.sh\"" );
}
