<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\LineBot\Tests;

use Symfony\Component\Notifier\Bridge\LineBot\LineBotTransportFactory;
use Symfony\Component\Notifier\Bridge\LineNotify\LineNotifyTransportFactory;
use Symfony\Component\Notifier\Test\AbstractTransportFactoryTestCase;
use Symfony\Component\Notifier\Test\IncompleteDsnTestTrait;

/**
 * @author Yi-Jyun Pan <me@pan93.com>
 */
final class LineBotTransportFactoryTest extends AbstractTransportFactoryTestCase
{
    use IncompleteDsnTestTrait;

    public function createFactory(): LineBotTransportFactory
    {
        return new LineBotTransportFactory();
    }

    public static function supportsProvider(): iterable
    {
        yield [true, 'linebot://host?receiver=abc'];
        yield [false, 'linebot://host'];
        yield [false, 'somethingElse://host'];
    }

    public static function createProvider(): iterable
    {
        yield [
            'linebot://host.test?receiver=abc',
            'linebot://token@host.test',
        ];
    }

    public static function incompleteDsnProvider(): iterable
    {
        yield 'missing token' => ['linenotify://host.test'];
    }

    public static function unsupportedSchemeProvider(): iterable
    {
        yield ['somethingElse://token@host'];
    }
}
