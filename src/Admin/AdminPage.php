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

namespace Yabe\AcssPurger\Admin;

use Yabe\AcssPurger\Plugin;
use Yabe\AcssPurger\Utils\Common;

class AdminPage
{
    public function __construct()
    {
        add_action('admin_menu', fn () => $this->add_admin_menu(), 1_000_001);
    }

    public static function get_page_url(): string
    {
        return add_query_arg([
            'page' => ACSS_PURGER_OPTION_NAMESPACE,
        ], admin_url('admin.php'));
    }

    public function add_admin_menu()
    {
        $hook = add_submenu_page('automatic-css', __('ACSS Purger', 'acss-purger'), __('ACSS Purger', 'acss-purger'), 'manage_options', ACSS_PURGER_OPTION_NAMESPACE, fn () => $this->render());

        add_action('load-' . $hook, fn () => $this->init_hooks());

        if (!class_exists('\_YabeWebfont\YABE_WEBFONT')) {
            if (get_template() === 'bricks') {
                add_submenu_page(
                    'bricks',
                    __('Self-host Google Fonts', 'acss-purger'),
                    __('Self-host Google Fonts', 'acss-purger'),
                    'manage_options',
                    'acss-purger-yabe-webfont-redirect',
                    static fn () => Common::redirect(admin_url('plugin-install.php?s=Rosua&tab=search&type=author')),
                    4
                );
            }

            if (defined('CT_PLUGIN_MAIN_FILE')) {
                add_submenu_page(
                    'ct_dashboard_page',
                    __('Self-host Google Fonts', 'acss-purger'),
                    __('Self-host Google Fonts', 'acss-purger'),
                    'manage_options',
                    'acss-purger-yabe-webfont-redirect',
                    static fn () => Common::redirect(admin_url('plugin-install.php?s=Rosua&tab=search&type=author'))
                );
            }
        }
    }

    private function render()
    {
        add_filter('admin_footer_text', fn ($text) => $this->admin_footer_text($text), 10001);
        echo '<div id="acss-purger-app" class=""></div>';
    }

    private function init_hooks()
    {
        add_action('admin_enqueue_scripts', fn () => $this->enqueue_scripts());
    }

    private function enqueue_scripts()
    {
        wp_enqueue_media();

        wp_enqueue_style(ACSS_PURGER_OPTION_NAMESPACE . '-app', plugin_dir_url(ACSS_PURGER_FILE) . 'build/app.css', [], filemtime(plugin_dir_path(ACSS_PURGER_FILE) . 'build/app.css'));
        wp_enqueue_script(ACSS_PURGER_OPTION_NAMESPACE . '-app', plugin_dir_url(ACSS_PURGER_FILE) . 'build/app.js', [], filemtime(plugin_dir_path(ACSS_PURGER_FILE) . 'build/app.js'), true);

        wp_set_script_translations(ACSS_PURGER_OPTION_NAMESPACE . '-app', 'acss-purger');
        wp_localize_script(ACSS_PURGER_OPTION_NAMESPACE . '-app', 'acssPurger', [
            '_version' => Plugin::VERSION,
            '_wpnonce' => wp_create_nonce(ACSS_PURGER_OPTION_NAMESPACE),
            'web_history' => self::get_page_url(),
            'rest_api' => [
                'nonce' => wp_create_nonce('wp_rest'),
                'root' => esc_url_raw(rest_url()),
                'namespace' => ACSS_PURGER_REST_NAMESPACE,
                'url' => esc_url_raw(rest_url(ACSS_PURGER_REST_NAMESPACE)),
            ],
            'assets' => [
                'url' => plugin_dir_url(ACSS_PURGER_FILE),
            ],
        ]);
    }

    private function admin_footer_text($text): string
    {
        return 'Thank you for using <b>ACSS Purger</b>! Join us on the <a href="https://l.suabahasa.dev/YkV8t" target="_blank">Facebook Group</a>.';
    }
}
