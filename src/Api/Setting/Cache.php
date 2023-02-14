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

namespace Yabe\AcssPurger\Api\Setting;

use Symfony\Component\Finder\Finder;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use Yabe\AcssPurger\Api\AbstractApi;
use Yabe\AcssPurger\Api\ApiInterface;
use Yabe\AcssPurger\Core\Cache as CoreCache;

class Cache extends AbstractApi implements ApiInterface
{
    public function __construct()
    {
    }

    public function get_prefix(): string
    {
        return 'setting/cache';
    }

    public function register_custom_endpoints(): void
    {
        register_rest_route(
            self::API_NAMESPACE,
            $this->get_prefix() . '/index',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => fn (\WP_REST_Request $wprestRequest): \WP_REST_Response => $this->index($wprestRequest),
                'permission_callback' => fn (\WP_REST_Request $wprestRequest): bool => $this->permission_callback($wprestRequest),
            ]
        );

        register_rest_route(
            self::API_NAMESPACE,
            $this->get_prefix() . '/generate',
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => fn (\WP_REST_Request $wprestRequest): \WP_REST_Response => $this->generate($wprestRequest),
                'permission_callback' => fn (\WP_REST_Request $wprestRequest): bool => $this->permission_callback($wprestRequest),
            ]
        );
    }

    public function index(WP_REST_Request $wprestRequest): WP_REST_Response
    {
        $cache = [
            'pending_task' => false,
            'last_generated' => '',
        ];

        $files = [
            'original' => [],
            'purged' => [],
        ];

        if (file_exists(ACSS_DYNAMIC_CSS_DIR)) {
            $finder = new Finder();
            $finder->files()->in(ACSS_DYNAMIC_CSS_DIR)->name('*.css');

            foreach ($finder as $f) {
                $files['original'][] = [
                    'name' => $f->getFilename(),
                    'size' => $f->getSize(),
                    'last_modified' => $f->getMTime(),
                    'file_url' => ACSS_DYNAMIC_CSS_URL . '/' . $f->getFilename(),
                ];
            }
        }

        if (file_exists(CoreCache::get_cache_path())) {
            $finder = new Finder();
            $finder->files()->in(CoreCache::get_cache_path())->name('*.css');

            foreach ($finder as $f) {
                $files['purged'][] = [
                    'name' => $f->getFilename(),
                    'size' => $f->getSize(),
                    'last_modified' => $f->getMTime(),
                    'file_url' => CoreCache::get_cache_url($f->getFilename()),
                ];
            }
        }

        if (wp_next_scheduled('a!yabe/acsspurger/core/cache:build_cache')) {
            $cache['pending_task'] = true;
        }

        return new WP_REST_Response([
            'cache' => $cache,
            'files' => $files,
        ]);
    }

    public function generate(WP_REST_Request $wprestRequest): WP_REST_Response
    {
        do_action('a!yabe/acsspurger/core/cache:build_cache');

        wp_clear_scheduled_hook('a!yabe/acsspurger/core/cache:build_cache');

        return $this->index($wprestRequest);
    }

    private function permission_callback(WP_REST_Request $wprestRequest): bool
    {
        return wp_verify_nonce($wprestRequest->get_header('X-WP-Nonce'), 'wp_rest') && current_user_can('manage_options');
    }
}
