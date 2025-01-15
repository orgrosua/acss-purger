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

use Automatic_CSS\Model\Config\Framework as AcssFramework;
use Automatic_CSS\Plugin as ACSSPlugin;
use Sabberworm\CSS\CSSList\CSSList;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Value\CalcFunction;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\Size;
use Symfony\Component\Finder\Finder;
use Yabe\AcssPurger\Plugin;
use Yabe\AcssPurger\Utils\Common;
use Yabe\AcssPurger\Utils\Config;
use Yabe\AcssPurger\Utils\Notice;

/**
 * Manage the cache of fonts for the frontpage.
 *
 * @author Joshua <id@rosua.org>
 */
class Cache
{
    /**
     * @var string
     */
    public const CACHE_DIR = '/acss-purger/cache/';

    public function __construct()
    {
        add_filter('cron_schedules', fn ($schedules) => $this->filter_cron_schedules($schedules));

        add_action('a!yabe/acsspurger/core/cache:build_cache', fn () => $this->build_cache());
        add_action('a!yabe/acsspurger/core/cache:schedule_cache', static fn () => static::schedule_cache());

        // listen to Config change for cache build (async/scheduled)
        add_action('f!yabe/acsspurger/api/setting/option:after_store', static fn () => self::schedule_cache(), 10, 1);

        add_action('a!yabe/acsspurger/plugins:deactivate_plugin_end', fn () => $this->drop_cache());

        add_action('automaticcss_settings_after_save', static fn () => static::schedule_cache());
    }

    public function filter_cron_schedules($schedules)
    {
        $schedules['acss_purger_cache'] = [
            'interval' => MINUTE_IN_SECONDS,
            'display' => __('Every minute', 'acss-purger'),
        ];

        return $schedules;
    }

    public static function schedule_cache(int $delay = 10)
    {
        if (! wp_next_scheduled('a!yabe/acsspurger/core/cache:build_cache')) {
            wp_schedule_single_event(time() + $delay, 'a!yabe/acsspurger/core/cache:build_cache');
        }
    }

    public static function get_cache_path(string $file_path = ''): string
    {
        return wp_upload_dir()['basedir'] . self::CACHE_DIR . $file_path;
    }

    public static function get_cache_url(string $file_path = ''): string
    {
        return wp_upload_dir()['baseurl'] . self::CACHE_DIR . $file_path;
    }

    public function build_cache()
    {
        $acss_plugin_info = get_plugin_data(ACSS_PLUGIN_FILE);

        if (! file_exists(ACSSPlugin::get_dynamic_css_dir())) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in(ACSSPlugin::get_dynamic_css_dir())->name('*.css');

        $classes = (new AcssFramework())->get_classes();

        $selectors = apply_filters('f!yabe/acsspurger/core/cache:selectors', []);

        $selectors = array_merge($selectors, Config::get('cache.safelist', []));

        $classes = array_filter($classes, static function ($class) use ($selectors) {
            foreach ($selectors as $selector) {
                if (false !== strpos($selector, '*')) {
                    if (preg_match(sprintf('/%s/', str_replace('*', '.*', $selector)), $class)) {
                        return false;
                    }

                } else {
                    if ($class === $selector) {
                        return false;
                    }
                }
            }

            return true;
        });

        $classes = array_map(static fn ($class) => sprintf('\.%s', $class), $classes);

        foreach ($finder as $file) {
            $raw = file_get_contents($file->getRealPath());

            $purged = self::purge_css($raw, $classes);

            $payload = sprintf(
                "/*\n! %s v%s | %s v%s | %s\n*/\n\n%s",
                Common::plugin_data('Name'),
                Plugin::VERSION,
                $acss_plugin_info['Name'],
                $acss_plugin_info['Version'],
                date('Y-m-d H:i:s', time()),
                $purged
            );

            try {
                Common::save_file($payload, self::get_cache_path($file->getFilename()));
            } catch (\Throwable $throwable) {
                Notice::error(sprintf('Failed to purge CSS: %s', $throwable->getMessage()));
            }
        }

        $this->purge_cache_plugin();
    }

    public function drop_cache()
    {
        if (! file_exists(self::get_cache_path())) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in(self::get_cache_path())->name('*.css');

        foreach ($finder as $file) {
            unlink($file->getRealPath());
        }
    }

    /**
     * @param string $raw the CSS content to purge
     * @param array<string> $selectors the substring of selectors to match
     */
    public static function purge_css($raw, $selectors): string
    {
        $parser = new Parser($raw);
        $cssDocument = $parser->parse();

        static::removeDeclarationBlockBySelector($cssDocument, $selectors);

        $css = $cssDocument->render();

        if (! defined('WP_DEBUG') || ! WP_DEBUG) {
            // remove new lines
            $css = preg_replace('#\n#', '', $css);

            // remove tabs
            $css = preg_replace('#\t#', '', $css);

            // remove multiple spaces
            $css = preg_replace('#\s+#', ' ', $css);
        }

        return $css;
    }

    /**
     * Removes a declaration block from the CSS list if it contain any given sub-string of selectors.
     *
     * @param Document $document the document to remove the declaration block from
     * @param array<string> $mSelectors the substring of selectors to match
     *
     * @see \Sabberworm\CSS\CSSList\CSSList::removeDeclarationBlockBySelector()
     */
    public static function removeDeclarationBlockBySelector(&$document, $mSelectors)
    {
        foreach ($document->getContents() as $iKey => &$mItem) {
            if (! ($mItem instanceof DeclarationBlock)) {
                if ($mItem instanceof CSSList) {
                    static::removeDeclarationBlockBySelector($mItem, $mSelectors);
                    if ($mItem->getContents() === []) {
                        $document->remove($mItem);
                    }
                }
                continue;
            }

            $itemSelectors = $mItem->getSelectors();
            foreach ($mSelectors as $mSelector) {
                foreach ($itemSelectors as $itemSelector) {
                    $pattern = strpos($mSelector, '*') === false ? '#%s(?![a-zA-Z0-9\_\-\,])#' : '#%s#';

                    if (preg_match(sprintf($pattern, $mSelector), $itemSelector->getSelector())) {
                        $mItem->removeSelector($itemSelector);
                    }
                }
            }

            if ($mItem->getSelectors() === []) {
                $document->remove($mItem);
            }

            if (Config::get('cache.remove_fallback', false)) {
                $mItem->getRules();

                $rules = $mItem->getRules();

                $rule_freq = array_count_values(array_map(static fn (Rule $rule) => $rule->getRule(), $rules));

                $rule_freq = array_filter($rule_freq, static fn ($v) => $v > 1);

                if ($rule_freq !== []) {
                    foreach ($rule_freq as $k => $freq) {
                        foreach ($rules as $rule) {
                            if ($rule->getRule() === $k) {
                                if ($rule->getValue() instanceof CSSFunction) {
                                    /** @var CSSFunction $r */
                                    $r = $rule->getValue();

                                    if ($r->getName() === 'clamp') {
                                        continue;
                                    }
                                }

                                if ($rule->getValue() instanceof Size) {
                                    $mItem->removeRule($rule);
                                    continue;
                                }

                                if ($freq === 3 && $rule->getValue() instanceof CalcFunction) {
                                    $mItem->removeRule($rule);
                                    continue;
                                }

                                if ($rule->getValue() instanceof RuleValueList) {
                                    /** @var RuleValueList $rList */
                                    $rList = $rule->getValue();

                                    $existCalc = false;
                                    $existClamp = false;

                                    foreach ($rList->getListComponents() as $rListItem) {
                                        if ($rListItem instanceof CalcFunction) {
                                            $existCalc = true;
                                        }

                                        if ($rListItem instanceof CSSFunction && $rListItem->getName() === 'clamp') {
                                            $existClamp = true;
                                        }
                                    }

                                    if (! $existCalc && ! $existClamp) {
                                        $mItem->removeRule($rule);
                                        continue;
                                    }

                                    if ($freq === 3 && ! $existClamp) {
                                        $mItem->removeRule($rule);
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Clear the cache from various cache plugins.
     */
    private function purge_cache_plugin()
    {
        /**
         * WordPress Object Cache
         * @see https://developer.wordpress.org/reference/classes/wp_object_cache/
         */
        \wp_cache_flush();

        /**
         * WP Rocket
         * @see https://docs.wp-rocket.me/article/92-rocketcleandomain
         */
        if (function_exists('rocket_clean_domain')) {
            \rocket_clean_domain();
        }

        /**
         * WP Super Cache
         * @see https://github.com/Automattic/wp-super-cache/blob/a0872032b1b3fc6847f490eadfabf74c12ad0135/wp-cache-phase2.php#L3013
         */
        if (function_exists('wp_cache_clear_cache')) {
            \wp_cache_clear_cache();
        }

        /**
         * W3 Total Cache
         * @see https://github.com/BoldGrid/w3-total-cache/blob/3a094493064ea60d727b3389dee813639860ef49/w3-total-cache-api.php#L259
         */
        if (function_exists('w3tc_flush_all')) {
            \w3tc_flush_all();
        }

        /**
         * WP Fastest Cache
         * @see https://www.wpfastestcache.com/tutorial/delete-the-cache-by-calling-the-function/
         */
        if (function_exists('wpfc_clear_all_cache')) {
            \wpfc_clear_all_cache(true);
        }

        /**
         * LiteSpeed Cache
         * @see https://docs.litespeedtech.com/lscache/lscwp/api/#purge-all-existing-caches
         */
        do_action('litespeed_purge_all');
    }
}
