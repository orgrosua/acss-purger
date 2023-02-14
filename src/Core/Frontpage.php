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

namespace Yabe\AcssPurger\Core;

use Symfony\Component\Finder\Finder;
use Yabe\AcssPurger\Core\Cache as CoreCache;

/**
 * Serve the font on the frontpage.
 *
 * @author Joshua <id@rosua.org>
 */
final class Frontpage
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', fn () => $this->enqueue_css_cache(), 1_000_001);
    }

    public function enqueue_css_cache()
    {
        global $wp_styles;

        $is_inside_editor = apply_filters('f!yabe/acsspurger/core/runtime:is_inside_editor', false);

        if ($is_inside_editor) {
            return;
        }

        if (! file_exists(CoreCache::get_cache_path())) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in(CoreCache::get_cache_path())->name('*.css');

        $cache_files = iterator_to_array($finder);

        array_walk($wp_styles->registered, static function ($style) use ($cache_files) {
            if (strpos($style->handle, 'automaticcss') !== false) {
                $file_name = pathinfo($style->src, PATHINFO_BASENAME);

                $filtered_cache_file = array_filter($cache_files, static fn ($file) => $file->getFilename() === $file_name);

                if ($filtered_cache_file !== []) {
                    $cache_file = array_shift($filtered_cache_file);
                    $style->src = CoreCache::get_cache_url($cache_file->getFilename());
                    $style->ver = $cache_file->getMTime();
                }
            }
        });
    }
}
