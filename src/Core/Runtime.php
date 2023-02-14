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

use Yabe\AcssPurger\Builder\Bricks;
use Yabe\AcssPurger\Builder\Oxygen;

/**
 * @author Joshua <id@rosua.org>
 */
class Runtime
{
    public function __construct()
    {
        new Bricks();
        new Oxygen();

        new Frontpage();
    }
}
