# plugin_name

<!--delete-->
---
This package can be used as to scaffold a WordPress plugin that can be published
to WordPress.org. This template includes a base plugin file, unit tests powered
by [Mantle](https://mantle.alley.co/), Continuous Integration [via GitHub
Actions](.github/workflows). Actions are configured to test the plugin and and
also build it for release. A `main-built` branch and `v0.1.0-built` tags will be
created with all dependencies included for use when submoduling a plugin.

Follow these steps to get started:

1. Press the "Use template" button at the top of this repo to create a new repo with the contents of this skeleton.
2. Run "php ./configure.php" to run a script that will replace all placeholders throughout all the files.
3. Have fun creating your plugin.
---
<!--/delete-->
plugin_description

## Installation

You can install the package via composer:

```bash
composer require vendor_slug/package_slug
```

## Usage

Activate the plugin in WordPress and use it like so:

```php
$plugin = Vendor_Name\Skeleton\Example_Plugin();
$plugin->perform_magic();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [author_name](https://github.com/author_name)
- [All Contributors](../../contributors)

## License

The GNU General Public License (GPL) license. Please see [License File](LICENSE) for more information.
