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
use Yabe\AcssPurger\Core\Cache;

/**
 * Oxygen Builder' worker integration
 *
 * @author Joshua Gugun Siagian <suabahasa@gmail.com>
 */
class Oxygen
{
    public function __construct()
    {
        add_filter('f!yabe/acsspurger/builder/oxygen:loop_content', fn (array $extracted, int $post_id): array => $this->loop_content($extracted, $post_id), -1, 2);

        add_action('plugins_loaded', fn () => $this->plugins_loaded(), 1_000_001);
    }

    public function plugins_loaded()
    {
        if (defined('CT_PLUGIN_MAIN_FILE')) {
            add_filter('f!yabe/acsspurger/core/cache:selectors', fn (array $selectors): array => $this->scan_contents($selectors), 10);

            add_filter('f!yabe/acsspurger/core/runtime:is_inside_editor', fn ($is) => $this->is_inside_editor($is));

            add_action('update_post_meta', fn ($_1, $_2, $meta_key) => $this->schedule_purge($meta_key), 1_000_001, 3);
        }
    }

    public function schedule_purge($meta_key)
    {
        if ($meta_key === 'ct_builder_json' || $meta_key === 'ct_builder_shortcodes') {
            Cache::schedule_cache(20);
        }
    }

    /**
     * @param bool $is
     * @return bool
     */
    public function is_inside_editor($is)
    {
        // iframe canvas
        if ((bool) filter_input(INPUT_GET, 'ct_builder')) {
            return true;
        }

        // editor page
        if ((bool) filter_input(INPUT_GET, 'oxygen_iframe')) {
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

        $ignoredPostTypes = [
            'attachment',
            'revision',
            'nav_menu_item',
            'custom_css',
            'customize_changeset',
            'oembed_cache',
        ];

        $postTypes = get_post_types();

        if (is_array($ignoredPostTypes) && is_array($postTypes)) {
            $postTypes = array_diff($postTypes, $ignoredPostTypes);
        }

        $postTypes = array_filter($postTypes, static fn ($val) => get_option('oxygen_vsb_ignore_post_type_' . $val, false) !== 'true');

        /**
         * @see oxygen_css_cache_generation_script()
         */
        $wpQuery = new WP_Query([
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_type' => $postTypes,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'ct_builder_shortcodes',
                    'value' => '',
                    'compare' => '!=',
                ],
                [
                    'key' => 'ct_builder_json',
                    'value' => '',
                    'compare' => '!=',
                ],
            ],
        ]);

        foreach ($wpQuery->posts as $post_id) {
            $posts[] = $post_id;
        }

        $extracted = [];

        foreach ($posts as $post) {
            /**
             * Produce the content of the markup from a single post
             *
             * @param array $extracted The content of the markup
             * @param int $post_id The ID of the post
             * @return array
             */
            $extracted = apply_filters('f!yabe/acsspurger/builder/oxygen:loop_content', $extracted, $post);
        }

        // filter duplicates
        $extracted = array_unique($extracted);

        /**
         * extract the classes from the content of the posts
         *
         * @param array $extracted The content of the markup
         * @param array $post_id The array of post IDs
         * @return array
         */
        return apply_filters('f!yabe/acsspurger/builder/oxygen:content_of_posts', array_merge($selectors, $extracted), $posts);
    }

    /**
     * @param array $extracted The extracted classes
     * @param int $post_id The ID of the post
     * @return array
     */
    public function loop_content($extracted, $post_id)
    {
        $shortcode = get_post_meta($post_id, 'ct_builder_json', true);
        $shortcode = json_decode($shortcode, true, 512, JSON_THROW_ON_ERROR);

        if (! $shortcode) {
            $shortcode = get_post_meta($post_id, 'ct_builder_shortcodes', true);
        }

        if (! is_array($shortcode)) {
            $shortcode = json_decode(oxygen_safe_convert_old_shortcodes_to_json($shortcode), true, 512, JSON_THROW_ON_ERROR);

            // exit if the shortcode is not an array
            if (! is_array($shortcode)) {
                return $extracted;
            }
        }

        return $this->extract_shortcode($shortcode, $extracted);
    }

    /**
     * Extract the classes from a parsed shortcode
     *
     * @param array $shortcode The parsed shortcode
     * @param array $extracted The array of classes
     * @return array
     */
    public function extract_shortcode($shortcode, $extracted)
    {
        if (array_key_exists('children', $shortcode)) {
            foreach ($shortcode['children'] as $child) {
                $extracted = $this->extract_shortcode($child, $extracted);
            }
        }

        if (array_key_exists('options', $shortcode)) {
            if (array_key_exists('plain_classes', $shortcode['options'])) {
                $plain_classes = $shortcode['options']['plain_classes'];

                // replace whitespaces into a whitespace
                $plain_classes = preg_replace('#\s+#', ' ', $plain_classes);

                // trim whitespace from begining and end
                $plain_classes = trim($plain_classes);

                // split into array
                $plain_classes = explode(' ', $plain_classes);

                // remove empty values
                $filtered = array_filter($plain_classes, static fn ($item) => ! empty(trim($item)));

                $extracted = array_merge($extracted, $filtered);
            }

            if (array_key_exists('classes', $shortcode['options'])) {
                $extracted = array_merge($extracted, $shortcode['options']['classes']);
            }
        }

        return $extracted;
    }
}
