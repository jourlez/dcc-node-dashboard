<?php
/**
 * Plugin Name: DCC Node Dashboard
 * Plugin URI: https://github.com/decentralchain/dcc-node-dashboard
 * Description: Node Dashboard for Decentralchain
 * Version: 0.0.0
 * Author: Josué Rojas
 * Author URI: https://github.com/decentralchain/dcc-node-dashboard
 * Requires at least: 5.9
 * Tested up to: 6.2
 *
 * Text Domain: dcc-node-dashboard
 * Domain Path: /languages/
 *
 * @package dcc-node-dashboard
 */

namespace DCC_Node_Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Root directory to this plugin.
 */
define( 'DCC_NODE_DASHBOARD_DIR', __DIR__ );

// Check if Composer is installed (remove if Composer is not required for your plugin).
if ( ! file_exists( __DIR__ . '/vendor/wordpress-autoload.php' ) ) {
	// Will also check for the presence of an already loaded Composer autoloader
	// to see if the Composer dependencies have been installed in a parent
	// folder. This is useful for when the plugin is loaded as a Composer
	// dependency in a larger project.
	if ( ! class_exists( \Composer\InstalledVersions::class ) ) {
		\add_action(
			'admin_notices',
			function () {
				?>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'Composer is not installed and dcc-node-dashboard cannot load. Try using a `*-built` branch if the plugin is being loaded as a submodule.', 'dcc-node-dashboard' ); ?></p>
				</div>
				<?php
			}
		);

		return;
	}
} else {
	// Load Composer dependencies.
	require_once __DIR__ . '/vendor/wordpress-autoload.php';
}

// Load the plugin's main files.
require_once __DIR__ . '/src/assets.php';
require_once __DIR__ . '/src/meta.php';

/**
 * Instantiate the plugin.
 */
function main(): void {
	// This should be an array with keys set to feature classnames and arguments.
	$features = [
		// Add initial features here.
	];
	$features = apply_filters( 'dcc_node_dashboard_features', $features );
	Feature_Manager::add_features( $features );
}
main();
