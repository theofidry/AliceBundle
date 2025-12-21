<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Hautelook\AliceBundle\PhpUnit;

use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Gary PEGEOT <garypegeot@gmail.com>
 */
#[CoversNothing]
class RecreateDatabaseTraitTest extends KernelTestCase
{
    use RecreateDatabaseTrait;
    use RefreshTestTrait;
}
