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
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class StreamOutputTest extends \PHPUnit_Framework_TestCase
{
    protected $stream;

    protected function setUp()
    {
        $this->stream = fopen('php://memory', 'a', false);
    }

    protected function tearDown()
    {
        $this->stream = null;
    }

    public function testConstructor()
    {
        $output = new MockStreamOutput($this->stream, Output::VERBOSITY_QUIET, true);
        $this->assertEquals($this->stream, $output->getStream(), '__construct() takes the decorated flag as its second argument');
        $this->assertEquals(Output::VERBOSITY_QUIET, $output->getVerbosity(), '__construct() takes the verbosity as its first argument');
        $this->assertTrue($output->isDecorated(), '__construct() takes the decorated flag as its second argument');

        try {
            $output = new MockStreamOutput('foo');
            $this->fail('->__construct() throws an \InvalidArgumentException when the stream is not valid');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->writeln() throws an \InvalidArgumentException when the type does not exist');
            $this->assertEquals('The StreamOutput class needs a stream as its first argument.', $e->getMessage());
        }
    }

    public function testGetDisplay()
    {
        $output = new MockStreamOutput($this->stream);
        $output->write('foobar');
        $this->assertEquals('foobar', $output->getDisplay(), '->getDisplay returns the readable stream');
    }

    public function testGetStream()
    {
        $output = new MockStreamOutput($this->stream);
        $this->assertEquals($this->stream, $output->getStream(), '->getStream() returns the current stream');
    }

    public function testDoWrite()
    {
        $output = new MockStreamOutput($this->stream);
        $output->writeln('foo');
        rewind($output->getStream());
        $this->assertEquals('foo'.PHP_EOL, stream_get_contents($output->getStream()), '->doWrite() writes to the stream');
    }

    public function testGetSetStatusCode()
    {
        $output = new MockStreamOutput($this->stream);
        $output->setStatusCode(12345);
        $this->assertEquals(12345, $output->getStatusCode(), '->doWrite() writes to the stream');
    }
}

class MockStreamOutput extends StreamOutput
{
}
