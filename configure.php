#!/usr/bin/env php
<?php
/**
 * Configure the WordPress Plugin interactively.
 *
 * phpcs:disable
 */

if ( ! defined( 'STDIN' ) ) {
	die( 'Not in CLI mode.' );
}

function ask( string $question, string $default = '' ): string {
	$answer = readline( $question . ( $default ? " ({$default})" : null ) . ': ' );

	if ( ! $answer ) {
		return $default;
	}

	return $answer;
}

function confirm( string $question, bool $default = false ): bool {
	$answer = ask( $question . ' (' . ( $default ? 'Y/n' : 'y/N' ) . ')' );

	if ( ! $answer ) {
		return $default;
	}

	return strtolower( $answer ) === 'y';
}

function writeln( string $line ): void {
	echo $line . PHP_EOL;
}

function run( string $command ): string {
	return trim( shell_exec( $command ) );
}

function str_after( string $subject, string $search ): string {
	$pos = strrpos( $subject, $search );

	if ( $pos === false ) {
		return $subject;
	}

	return substr( $subject, $pos + strlen( $search ) );
}

function slugify( string $subject ): string {
	return strtolower( trim( preg_replace( '/[^A-Za-z0-9-]+/', '-', $subject ), '-' ) );
}

function title_case( string $subject ): string {
	return ensure_capitalp( str_replace( ' ', '_', ucwords( str_replace( [ '-', '_' ], ' ', $subject ) ) ) );
}

function ensure_capitalp( string $text ): string {
	return str_replace( 'Wordpress', 'WordPress', $text );
}

function replace_in_file( string $file, array $replacements ): void {
	$contents = file_get_contents( $file );

	file_put_contents(
		$file,
		str_replace(
			array_keys( $replacements ),
			array_values( $replacements ),
			$contents
		)
	);
}

function remove_readme_paragraphs( string $file ): void {
	$contents = file_get_contents( $file );

	file_put_contents(
		$file,
		trim( preg_replace( '/<!--delete-->.*<!--\/delete-->/s', '', $contents ) ?: $contents ),
	);
}

function remove_composer_require( string $file = 'plugin.php' ) {
	$contents = file_get_contents( $file );

	file_put_contents(
		$file,
		trim( preg_replace( '/\/\/ Check if Composer.*vendor\/autoload\.php\';\\n\\n?/s', '', $contents ) ?: $contents ),
	);
}

function remove_assets_readme( bool $keep_contents, string $file = 'README.md' ) {
	$contents = file_get_contents( $file );

	if ( $keep_contents ) {
		$contents = str_replace( '<!--front-end-->', '', $contents );
		$contents = str_replace( '<!--/front-end-->', '', $contents );

		file_put_contents( $file, $contents );
	} else {
		file_put_contents(
			$file,
			trim( preg_replace( '/<!--front-end-->.*<!--\/front-end-->/s', '', $contents ) ?: $contents ),
		);
	}
}

function remove_assets_require( string $file = 'plugin.php' ) {
	$contents = file_get_contents( $file );

	file_put_contents(
		$file,
		trim( preg_replace( '/require_once __DIR__ \. \'\/src\/assets.php\';\\n/s', '', $contents ) ?: $contents ),
	);
}

function determine_separator( string $path ): string {
	return str_replace( '/', DIRECTORY_SEPARATOR, $path );
}

function list_all_files_for_replacement(): array {
	return explode( PHP_EOL, run( 'grep -R -l ./  --exclude={LICENSE,configure.php} --exclude-dir={.git,.github,vendor,bin,webpack,node_modules}' ) );
}

if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( string $haystack, string $needle ): bool {
		return '' === $needle || false !== strpos( $haystack, $needle );
	}
}

function delete_files( string|array $paths ) {
	if ( ! is_array( $paths ) ) {
		$paths = [ $paths ];
	}

	foreach ( $paths as $path ) {
		if ( is_dir( $path ) ) {
			run( "rm -rf {$path}" );
		} elseif ( file_exists( $path ) ) {
			unlink( $path );
		}
	}
}

echo "\nWelcome friend! 😀\nLet's setup your WordPress Plugin 🚀\n\n";

$git_name    = run( 'git config user.name' );
$author_name = ask( 'Author name', $git_name );

$git_email    = run( 'git config user.email' );
$author_email = ask( 'Author email', $git_email );

$username_guess  = explode( ':', run( 'git config remote.origin.url' ) )[1];
$username_guess  = dirname( $username_guess );
$username_guess  = basename( $username_guess );
$author_username = ask( 'Author username', $username_guess );

$vendor_name      = ask( 'Vendor name (usually the Github Organization)', $username_guess );
$vendor_slug      = slugify( $vendor_name );

$current_dir = getcwd();
$folder_name = ensure_capitalp( basename( $current_dir ) );

$plugin_name      = ask( 'Plugin name', str_replace( '_', ' ', title_case( $folder_name ) ) );
$plugin_name_slug = slugify( $plugin_name );

$namespace  = ask( 'Plugin namespace', title_case( $plugin_name ) );
$class_name = ask( 'Base class name for plugin', title_case( $plugin_name ) );

$description = ask( 'Plugin description', "This is my plugin {$plugin_name}" );

writeln( '------' );
writeln( "Author      : {$author_name} ({$author_email})" );
writeln( "Vendor      : {$vendor_name} ({$vendor_slug})" );
writeln( "Plugin      : {$plugin_name} <{$plugin_name_slug}>" );
writeln( "Description : {$description}" );
writeln( "Namespace   : {$namespace}" );
writeln( "Main Class  : {$class_name}" );
writeln( '------' );

writeln( 'This script will replace the above values in all relevant files in the project directory.' );

if ( ! confirm( 'Modify files?', true ) ) {
	exit( 1 );
}

if ( 0 === strpos( strtoupper( PHP_OS ), 'WIN' ) ) {
	die( 'Not supported in Windows.' );
}

$search_and_replace = [
	'author_name'             => $author_name,
	'author_username'         => $author_username,
	'email@domain.com'        => $author_email,

	'A skeleton WordPress plugin' => $description,

	'Create_WordPress_Plugin' => $namespace,
	'Example_Plugin'          => $class_name,

	'create_wordpress_plugin' => str_replace( '-', '_', $plugin_name ),
	'plugin_name'             => $plugin_name,

	'create-wordpress-plugin' => $plugin_name_slug,
	'Create WordPress Plugin' => $plugin_name,

	'CREATE_WORDPRESS_PLUGIN' => strtoupper( str_replace( '-', '_', $plugin_name ) ),
	'Skeleton'                => $class_name,
	'vendor_name'             => $vendor_name,
	'alleyinteractive'        => $vendor_slug,
];

foreach ( list_all_files_for_replacement() as $path ) {
	echo "Updating $path...\n";
	replace_in_file( $path, $search_and_replace );

	if ( str_contains( $path, determine_separator( 'inc/class-example-plugin.php' ) ) ) {
		rename( $path, determine_separator( './inc/class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php' ) );
	}

	if ( str_contains( $path, 'README.md' ) ) {
		remove_readme_paragraphs( $path );
	}
}

echo "Done!\n\n";'Done!' . PHP_EOL;

$needs_built_assets = false;

if ( confirm( 'Will this plugin be using Composer? (WordPress Composer Autoloader already included!)' ) ) {
	$needs_built_assets = true;

	if ( confirm( 'Execute `composer install`?', true ) ) {
		if ( file_exists( __DIR__ . '/composer.lock' ) ) {
			echo run( 'composer update' );
		} else {
			echo run( 'composer install' );
		}

		echo "\n\n";
	}
} elseif ( confirm( 'Do you want to remove the vendor/autoload.php dependency from your main plugin file?' ) ) {
	remove_composer_require();
}

if ( confirm( 'Will this plugin be compiling front-end assets (Node)?', true ) ) {
	$needs_built_assets = true;

	if ( confirm( 'Do you want to run `npm install` and `npm run build`?', true ) ) {
		echo run( 'npm install && npm run build' );
	}

	remove_assets_readme( true );
} elseif ( confirm( 'Do you want to delete the front-end files? (Such as package.json, webpack.config.js, etc.)', true ) ) {
	echo "Deleting...\n";

	delete_files(
		[
			'.github/workflows/node-tests.yml',
			'.eslintignore',
			'.eslintrc.json',
			'.nvmrc',
			'.stylelintrc.json',
			'babel.config.json',
			'jsconfig.json',
			'package.json',
			'package-lock.json',
			'webpack.config.js',
			'webpack/',
			'entries/',
			'services/',
			'slotfills/',
			'build/',
			'bin/',
			'node_modules/',
			'scaffold',
			'src/assets.php',
		]
	);

	remove_assets_readme( false );
	remove_assets_require();
}

if ( ! $needs_built_assets && confirm( 'Delete the Github actions for built assets?' ) ) {
	delete_files(
		[
			'.github/workflows/built-branch.yml',
			'.github/workflows/built-tag.yml',
		]
	);
}

if ( confirm( 'Let this script delete itself?', true ) ) {
	delete_files(
		[
			'Makefile',
			__FILE__,
		]
	);
}

echo "\n\nWe're done! 🎉\n\n";
