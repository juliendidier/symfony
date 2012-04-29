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
use Symfony\Component\Console\Output\ErrorOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ErrorOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderException()
    {
        $e = new \RuntimeException('foo');
        $output = new ErrorOutput(fopen('php://memory', 'w'));
        $output->renderException($e);
        $this->assertEquals($this->getFooError(), $output->getDisplay());

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        $output->renderException(new \RuntimeException('foo'));
        $this->assertContains('Exception trace:', $output->getDisplay());
    }

    protected function getFooError()
    {
        return <<<CONSOLE


                      
  [RuntimeException]  
  foo                 
                      



CONSOLE;
    }
}

