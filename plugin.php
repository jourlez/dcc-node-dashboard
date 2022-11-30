<?php
/**
 * Plugin Name: Create WordPress Plugin
 * Plugin URI: https://github.com/alleyinteractive/create-wordpress-plugin
 * Description: A skeleton WordPress plugin
 * Version: 0.1.0
 * Author: author_name
 * Author URI: https://github.com/alleyinteractive/create-wordpress-plugin
 * Requires at least: 5.9
 * Tested up to: 5.9
 *
 * Text Domain: create-wordpress-plugin
 * Domain Path: /languages/
 *
 * @package create-wordpress-plugin
 */

namespace Create_WordPress_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Root directory to this plugin.
 *
 * @var string
 */
define( 'CREATE_WORDPRESS_PLUGIN_DIR', __DIR__ );

// Check if Composer is installed (remove if Composer is not required for your plugin).
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	\add_action(
		'admin_notices',
		function() {
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'Composer is not installed and create-wordpress-plugin cannot load. Try using a `*-built` branch if the plugin is being loaded as a submodule.', 'plugin_domain' ); ?></p>
			</div>
			<?php
		}
	);

	return;
}

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

// Load the plugin's main files.
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/meta.php';

/**
 * Load the php index files from the build directory for blocks, slotfills, and any other scripts with an index.php file.
 */
function load_scripts() {
	foreach ( glob( __DIR__ . '/build/**/index.php' ) as $path ) {
		if ( 0 === validate_file( $path ) && file_exists( $path ) ) {
			require_once $path;  // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.IncludingFile, WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}
	}
}

load_scripts();

/**
 * Instantiate the plugin.
 */
function main() {
	// ...
}
main();
