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

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use Yabe\AcssPurger\Api\AbstractApi;
use Yabe\AcssPurger\Api\ApiInterface;

class Option extends AbstractApi implements ApiInterface
{
    public function __construct()
    {
    }

    public function get_prefix(): string
    {
        return 'setting/option';
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
            $this->get_prefix() . '/store',
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => fn (\WP_REST_Request $wprestRequest): \WP_REST_Response => $this->store($wprestRequest),
                'permission_callback' => fn (\WP_REST_Request $wprestRequest): bool => $this->permission_callback($wprestRequest),
            ]
        );
    }

    public function index(WP_REST_Request $wprestRequest): WP_REST_Response
    {
        $options = json_decode(get_option(ACSS_PURGER_OPTION_NAMESPACE . '_options', '{}'), null, 512, JSON_THROW_ON_ERROR);

        $options = apply_filters('f!yabe/acsspurger/api/setting/option:index_options', $options);

        return new WP_REST_Response([
            'options' => $options,
        ]);
    }

    public function store(WP_REST_Request $wprestRequest): WP_REST_Response
    {
        $payload = $wprestRequest->get_json_params();

        $options = $payload['options'];

        $options = apply_filters('f!yabe/acsspurger/api/setting/option:store_options', $options);

        update_option(ACSS_PURGER_OPTION_NAMESPACE . '_options', json_encode($options, JSON_THROW_ON_ERROR));

        do_action('f!yabe/acsspurger/api/setting/option:after_store', $options);

        return $this->index($wprestRequest);
    }

    private function permission_callback(WP_REST_Request $wprestRequest): bool
    {
        return wp_verify_nonce($wprestRequest->get_header('X-WP-Nonce'), 'wp_rest') && current_user_can('manage_options');
    }
}
