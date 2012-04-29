<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Output;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\ErrorOutput;

class ConsoleOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $output = new ConsoleOutput(Output::VERBOSITY_QUIET, true);
        $this->assertEquals(Output::VERBOSITY_QUIET, $output->getVerbosity(), '__construct() takes the verbosity as its first argument');
        $this->assertTrue($output->isDecorated(), '__construct() takes the decorated as its second argument');
    }

    public function testGetErrorOutput()
    {
        $output = new ConsoleOutput();
        $this->assertTrue($output->getErrorOutput() instanceof ErrorOutput);
    }

    public function testSetDecorated()
    {
        $output = new ConsoleOutput();
        $output->setDecorated(true);
        $this->assertTrue($output->isDecorated());
        $this->assertTrue($output->getErrorOutput()->isDecorated());
    }

    public function testSetFormatter()
    {
        $output = new ConsoleOutput();
        $formater = new OutputFormatter();
        $output->setFormatter($formater);
        $this->assertEquals($formater, $output->getFormatter());
        $this->assertEquals($formater, $output->getErrorOutput()->getFormatter());
    }

    public function testSetVerbosity()
    {
        $output = new ConsoleOutput();
        $output->setVerbosity(10);
        $this->assertEquals(10, $output->getVerbosity());
        $this->assertEquals(10, $output->getErrorOutput()->getVerbosity());
    }
}
