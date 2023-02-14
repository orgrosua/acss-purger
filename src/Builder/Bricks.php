<?php

/*
 * This file is part of the Wakaloka\Winden package.
 *
 * (c) Joshua Gugun Siagian <suabahasa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Yabe\AcssPurger\Builder;

use WP_Query;

/**
 * Bricks Builder' worker integration
 *
 * @author Joshua Gugun Siagian <suabahasa@gmail.com>
 */
class Bricks
{
    public function __construct()
    {
        add_filter('f!yabe/acsspurger/builder/bricks:loop_content', fn (array $extracted, int $post_id): array => $this->loop_content($extracted, $post_id), -1, 2);

        add_action('after_setup_theme', fn () => $this->after_setup_theme(), 1_000_001);
    }

    public function after_setup_theme()
    {
        if (defined('BRICKS_VERSION')) {
            add_filter('f!yabe/acsspurger/core/cache:selectors', fn (array $selectors): array => $this->scan_contents($selectors), 10);

            add_filter('f!yabe/acsspurger/core/runtime:is_inside_editor', fn ($is) => $this->is_inside_editor($is));
        }
    }

    /**
     * @param bool $is
     * @return bool
     */
    public function is_inside_editor($is)
    {
        // iframe canvas
        if (filter_input(INPUT_GET, 'brickspreview') !== null) {
            return true;
        }

        // editor page
        if (filter_input(INPUT_GET, 'bricks') === 'run') {
            return true;
        }

        return $is;
    }

    /**
     * Scan the data to catch all selectors being used
     *
     * @param array $selectors The selectors
     * @return array
     */
    public function scan_contents($selectors)
    {
        $posts = [];

        $post_types = \Bricks\Database::$global_settings['postTypes'] ?? [];

        $post_types[] = BRICKS_DB_TEMPLATE_SLUG;

        $wpQuery = new WP_Query([
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_type' => $post_types,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => BRICKS_DB_PAGE_HEADER,
                ],
                [
                    'key' => BRICKS_DB_PAGE_CONTENT,
                ],
                [
                    'key' => BRICKS_DB_PAGE_FOOTER,
                ],
            ],
        ]);

        foreach ($wpQuery->posts as $post_id) {
            $posts[] = $post_id;
        }

        $extracted = [];

        foreach ($posts as $post) {
            /**
             * @param array $class The extracted classes
             * @param int $post_id The ID of the post
             * @return array
             */
            $extracted = apply_filters('f!yabe/acsspurger/builder/bricks:loop_content', $extracted, $post);
        }

        $global_classes_index = [];

        foreach (get_option(BRICKS_DB_GLOBAL_CLASSES, []) as $value) {
            $global_classes_index[$value['id']] = $value['name'];
        }

        // swap the classes with actual names (BRICKS_DB_GLOBAL_CLASSES)
        $extracted = array_map(static fn ($class) => array_key_exists($class, $global_classes_index) ? $global_classes_index[$class] : $class, $extracted);

        // filter duplicates
        $extracted = array_unique($extracted);

        /**
         * extract the classes from the content of the posts
         *
         * @param array $extracted The extracted classes
         * @param array $post_id The array of post IDs
         * @return array
         */
        return apply_filters('f!yabe/acsspurger/builder/bricks:content_of_posts', array_merge($selectors, $extracted), $posts);
    }

    /**
     * @param array $extracted The extracted classes
     * @param int $post_id The ID of the post
     * @return array
     */
    public function loop_content($extracted, $post_id)
    {
        $post_meta_keys = [
            BRICKS_DB_PAGE_HEADER,
            BRICKS_DB_PAGE_CONTENT,
            BRICKS_DB_PAGE_FOOTER,
        ];

        foreach ($post_meta_keys as $post_metum_key) {
            $meta_value = get_post_meta($post_id, $post_metum_key, true);
            if ($meta_value) {
                foreach ($meta_value as $metum_value) {
                    // classes
                    if (array_key_exists('settings', $metum_value) && array_key_exists('_cssGlobalClasses', $metum_value['settings'])) {
                        $extracted = array_merge($extracted, $metum_value['settings']['_cssGlobalClasses']);
                    }
                }
            }
        }

        return $extracted;
    }
}
