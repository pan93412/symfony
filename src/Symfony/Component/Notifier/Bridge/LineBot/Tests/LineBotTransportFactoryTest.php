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
        yield [true, 'linebot://host?receiver=abc&token=token'];
        yield [true, 'linebot://host'];
        yield [false, 'somethingElse://host'];
    }

    public static function createProvider(): iterable
    {
        yield [
            'linebot://api.line.me?receiver=test',
            'linebot://default?receiver=test&token=eyJhbGciOiJIUzI1NiJ9.eyJSb2xlIjoiQWRtaW4iLCJJc3N1ZXIiOiJJc3N1ZXIiLCJVc2VybmFtZSI6IkphdmFJblVzZSIsImV4cCI6MTcyODU1MjA3OSwiaW+F0IjoxNzI4NTUyMDc5fQ.SPKpGKwsXBay2uXDh7tATW20S2vZpw9qcmYjNp46Ir/AB/12345677=',
        ];
    }

    public static function incompleteDsnProvider(): iterable
    {
        yield 'missing token' => ['linebot://host.test'];
    }

    public static function unsupportedSchemeProvider(): iterable
    {
        yield ['somethingElse://token@host'];
        yield ['somethingElse://token@host?receiver=abc&token=token'];
    }
}
