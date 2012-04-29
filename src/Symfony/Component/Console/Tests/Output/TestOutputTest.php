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

use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\TestOutput;

class TestOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $output = new TestOutput(Output::VERBOSITY_QUIET, true);
        $this->assertEquals(Output::VERBOSITY_QUIET, $output->getVerbosity(), '__construct() takes the verbosity as its first argument');
        $this->assertTrue($output->isDecorated(), '__construct() takes the decorated flag as its second argument');
    }

    public function testDoWrite()
    {
        $output = new TestOutput();
        $output->writeln('foo');
        rewind($output->getStream());
        $this->assertEquals('foo'.PHP_EOL, stream_get_contents($output->getStream()), '->doWrite() writes to the stream');
    }

    public function testGetErrorOutput()
    {
        $output = new TestOutput();
        $this->assertEquals($output, $output->getErrorOutput(), '->getErrorOutput returns the same instance');
    }

    public function testIsSuccessful()
    {
        $output = new TestOutput();
        $output->setStatusCode(0);
        $this->assertTrue($output->isSuccessful(), '->isSuccessful() returns true');

        $output->setStatusCode(1);
        $this->assertFalse($output->isSuccessful(), '->isSuccessful() returns false');
    }

    public function testRenderException()
    {
        $e = new \InvalidArgumentException('foo');
        $output = new TestOutput(fopen('php://memory', 'w'));
        $output->renderException($e);

        $this->assertEquals($this->getFooError(), $output->getDisplay());
        $this->assertEquals($e->getMessage(), $output->getErrorMessage());

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        $output->renderException(new \RuntimeException('foo'));
        $this->assertContains('Exception trace:', $output->getDisplay(), '->testRenderException returns the Exception trace');

    }

    protected function getFooError()
    {
        return <<<CONSOLE


                              
  [InvalidArgumentException]  
  foo                         
                              



CONSOLE;
    }
}
