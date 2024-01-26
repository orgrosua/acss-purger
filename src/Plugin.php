<?php

/*
 * This file is part of the Yabe package.
 *
 * (c) Joshua <id@rosua.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Yabe\AcssPurger;

use Exception;
use Yabe\AcssPurger\Admin\AdminPage;
use Yabe\AcssPurger\Api\Router as ApiRouter;
use Yabe\AcssPurger\Core\Cache;
use Yabe\AcssPurger\Core\Runtime;
use Yabe\AcssPurger\Utils\Notice;

/**
 * Manage the plugin lifecycle and provides a single point of entry to the plugin.
 *
 * @author Joshua <id@rosua.org>
 */
final class Plugin
{
    /**
     * @var string
     */
    public const VERSION = '1.0.13';

    /**
     * @var int
     */
    public const VERSION_ID = 10013;

    /**
     * @var int
     */
    public const MAJOR_VERSION = 1;

    /**
     * @var int
     */
    public const MINOR_VERSION = 0;

    /**
     * @var int
     */
    public const RELEASE_VERSION = 13;

    /**
     * @var string
     */
    public const EXTRA_VERSION = '';

    /**
     * Stores the instance, implementing a Singleton pattern.
     */
    private static self $instance;

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct()
    {
    }

    /**
     * Singletons should not be cloneable.
     */
    private function __clone()
    {
    }

    /**
     * Singletons should not be restorable from strings.
     *
     * @throws Exception Cannot unserialize a singleton.
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize a singleton.');
    }

    /**
     * This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static property. On subsequent runs, it returns the client existing
     * object stored in the static property.
     */
    public static function get_instance(): self
    {
        $cls = static::class;
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function boot_debug()
    {
        // if (WP_DEBUG === true && class_exists(\Sentry\SentrySdk::class)) {
        // }
    }

    public function boot_migration()
    {
        // new Migration();
    }

    /**
     * Boot to the Plugin.
     */
    public function boot(): void
    {
        do_action('a!yabe/acsspurger/plugins:boot_start');

        $this->boot_debug();
        $this->boot_migration();

        // (de)activation hooks.
        register_activation_hook(ACSS_PURGER_FILE, function (): void {
            $this->activate_plugin();
        });
        register_deactivation_hook(ACSS_PURGER_FILE, function (): void {
            $this->deactivate_plugin();
        });

        // upgrade hooks.
        add_action('upgrader_process_complete', function ($upgrader, $options): void {
            if ($options['action'] === 'update' && $options['type'] === 'plugin') {
                foreach ($options['plugins'] as $plugin) {
                    if ($plugin === plugin_basename(ACSS_PURGER_FILE)) {
                        $this->upgrade_plugin();
                    }
                }
            }
        }, 10, 2);

        new Cache();
        new Runtime();
        new ApiRouter();

        // admin hooks.
        if (is_admin()) {
            add_filter('plugin_action_links_' . plugin_basename(ACSS_PURGER_FILE), fn ($links) => $this->plugin_action_links($links));

            add_action('plugins_loaded', function (): void {
                $this->plugins_loaded_admin();
            }, 100);

            new AdminPage();

            do_action('a!yabe/acsspurger/plugins:boot_admin');
        }

        do_action('a!yabe/acsspurger/plugins:boot_end');
    }

    /**
     * Handle the plugin's activation
     */
    public function activate_plugin(): void
    {
        do_action('a!yabe/acsspurger/plugins:activate_plugin_start');

        update_option(ACSS_PURGER_OPTION_NAMESPACE . '_version', self::VERSION);

        do_action('a!yabe/acsspurger/plugins:activate_plugin_end');
    }

    /**
     * Handle plugin's deactivation by (maybe) cleaning up after ourselves.
     */
    public function deactivate_plugin(): void
    {
        do_action('a!yabe/acsspurger/plugins:deactivate_plugin_start');
        // TODO: Add deactivation logic here.
        do_action('a!yabe/acsspurger/plugins:deactivate_plugin_end');
    }

    /**
     * Handle the plugin's upgrade
     */
    public function upgrade_plugin(): void
    {
        do_action('a!yabe/acsspurger/plugins:upgrade_plugin_start');
        // TODO: Add upgrade logic here.
        do_action('a!yabe/acsspurger/plugins:upgrade_plugin_end');
    }

    /**
     * Warm up the plugin for admin.
     */
    public function plugins_loaded_admin(): void
    {
        load_plugin_textdomain('acss-purger', false, dirname(plugin_basename(ACSS_PURGER_FILE)) . '/translations/');

        add_action('admin_notices', static function () {
            $messages = Notice::get_lists();
            if ($messages && is_array($messages)) {
                foreach ($messages as $message) {
                    echo sprintf(
                        '<div class="notice notice-%s is-dismissible %s">%s</div>',
                        esc_attr($message['status']),
                        ACSS_PURGER_OPTION_NAMESPACE,
                        esc_html($message['message'])
                    );
                }
            }
        }, 100);
    }

    /**
     * Add plugin action links.
     *
     * @param array<string> $links
     * @return array<string>
     */
    public function plugin_action_links(array $links): array
    {
        $base_url = AdminPage::get_page_url();

        array_unshift($links, sprintf(
            '<a href="%s">%s</a>',
            esc_url(sprintf('%s#/settings', $base_url)),
            esc_html__('Settings', 'acss-purger')
        ));

        return $links;
    }
}
