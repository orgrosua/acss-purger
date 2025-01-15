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
        add_filter('style_loader_src', fn ($src, $handle) => $this->filter_style_loader_src($src, $handle), 1_000_001, 2);
    }

    public function filter_style_loader_src($src, $handle)
    {
        // only serve the original css file to admin
        if (current_user_can('manage_options')) {
            return $src;
        }

        $is_inside_editor = apply_filters('f!yabe/acsspurger/core/runtime:is_inside_editor', false);

        if ($is_inside_editor) {
            return $src;
        }

        $finder = new Finder();
        $finder->files()->in(CoreCache::get_cache_path())->name('*.css');

        $cache_files = iterator_to_array($finder);

        if (strpos($handle, 'automaticcss') !== false) {
            $file_name = pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_BASENAME);
            $filtered_cache_file = array_filter($cache_files, static fn ($file) => $file->getFilename() === $file_name);

            if ($filtered_cache_file !== []) {
                $cache_file = array_shift($filtered_cache_file);
                return CoreCache::get_cache_url($cache_file->getFilename());
            }
        }

        return $src;
    }
}
