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

namespace Yabe\AcssPurger\Api;

use ReflectionClass;
use Symfony\Component\Finder\Finder;

class Router
{
    /**
     * List of APIs services.
     *
     * @var ApiInterface[]
     */
    private array $apis = [];

    public function __construct()
    {
        $this->scan_apis();
        add_action('rest_api_init', function () {
            $this->register_apis();
        });
    }

    public function scan_apis()
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__)->name('*.php');

        /**
         * Add additional places to scan for APIs.
         *
         * @param Finder $finder The Finder instance.
         */
        do_action('a!yabe/acsspurger/api/router:before_scan', $finder);

        foreach ($finder as $file) {
            $api_file = $file->getPathname();

            if (! is_readable($api_file)) {
                continue;
            }

            require_once $api_file;
        }

        // Find any APIs that extends ApiInterface class
        $declared_classes = get_declared_classes();

        foreach ($declared_classes as $declared_class) {
            if (! class_exists($declared_class)) {
                continue;
            }

            $reflector = new ReflectionClass($declared_class);

            if (! $reflector->isSubclassOf(ApiInterface::class)) {
                continue;
            }

            // Get API detail and push to Router::$apis to be register later
            /** @var ApiInterface $api */
            $api = $reflector->newInstanceWithoutConstructor();

            $this->apis[$api->get_prefix()] = [
                'name' => $api->get_prefix(),
                'file_path' => $reflector->getFileName(),
                'class_name' => $reflector->getName(),
            ];
        }
    }

    /**
     * Register APIs.
     */
    public function register_apis(): void
    {
        /**
         * Filter the APIs before register to WP Rest.
         *
         * @param ApiInterface[] $apis
         * @return ApiInterface[]
         */
        /** @var ApiInterface[] $apis */
        $apis = apply_filters('f!yabe/acsspurger/api/router:register_apis', $this->apis);

        foreach ($apis as $api) {
            // Create new instance of API class and register custom endpoints
            /** @var ApiInterface $apiInstance */
            $apiInstance = new $api['class_name']();
            $apiInstance->register_custom_endpoints();
        }
    }
}
