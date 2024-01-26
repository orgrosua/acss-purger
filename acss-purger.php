<?php

/**
 * ACSS Purger
 *
 * @wordpress-plugin
 * Plugin Name:         ACSS Purger
 * Plugin URI:          https://acss-purger.yabe.land
 * Description:         Purge Automatic.css CSS file
 * Version:             1.0.12
 * Requires at least:   6.0
 * Requires PHP:        7.4
 * Author:              Rosua
 * Author URI:          https://acss-purger.yabe.land
 * Donate link:         https://ko-fi.com/Q5Q75XSF7
 * Text Domain:         acss-purger
 * Domain Path:         /languages
 *
 * @package             Yabe
 * @author              Joshua <id@rosua.org>
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

define('ACSS_PURGER_FILE', __FILE__);

/**
 * Namespace or prefix of the Package's on the wp_options table.
 */
define('ACSS_PURGER_OPTION_NAMESPACE', 'acss_purger');

define('ACSS_PURGER_REST_NAMESPACE', 'acss-purger/v1');

if (file_exists(__DIR__ . '/vendor/scoper-autoload.php')) {
    require_once __DIR__ . '/vendor/scoper-autoload.php';
} else {
    require_once __DIR__ . '/vendor/autoload.php';
}

\Yabe\AcssPurger\Plugin::get_instance()->boot();
