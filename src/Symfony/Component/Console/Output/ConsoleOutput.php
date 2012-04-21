<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * ConsoleOutput is the default class for all CLI output. It uses STDOUT.
 *
 * This class is a convenient wrapper around `StreamOutput`.
 *
 *     $output = new ConsoleOutput();
 *
 * This is equivalent to:
 *
 *     $output = new StreamOutput(fopen('php://stdout', 'w'));
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class ConsoleOutput extends StreamOutput implements ConsoleOutputInterface
{
    private $stderr;

    /**
     * Constructor.
     *
     * @param integer         $verbosity The verbosity level (self::VERBOSITY_QUIET, self::VERBOSITY_NORMAL,
     *                                   self::VERBOSITY_VERBOSE)
     * @param Boolean         $decorated Whether to decorate messages or not (null for auto-guessing)
     * @param OutputFormatter $formatter Output formatter instance
     *
     * @api
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        parent::__construct(fopen('php://stdout', 'w'), $verbosity, $decorated, $formatter);
        $this->stderr = new StreamOutput(fopen('php://stderr', 'w'), $verbosity, $decorated, $formatter);
    }

    public function setDecorated($decorated)
    {
        parent::setDecorated($decorated);
        $this->stderr->setDecorated($decorated);
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
        parent::setFormatter($formatter);
        $this->stderr->setFormatter($formatter);
    }

    public function setVerbosity($level)
    {
        parent::setVerbosity($level);
        $this->stderr->setVerbosity($level);
    }

    /**
     * @return OutputInterface
     */
    public function getErrorOutput()
    {
        return $this->stderr;
    }

    public function setErrorOutput(OutputInterface $error)
    {
        $this->stderr = $error;
    }

    public function renderException(\Exception $e)
    {
        $strlen = function ($string) {
            if (!function_exists('mb_strlen')) {
                return strlen($string);
            }

            if (false === $encoding = mb_detect_encoding($string)) {
                return strlen($string);
            }

            return mb_strlen($string, $encoding);
        };

        do {
            $title = sprintf('  [%s]  ', get_class($e));
            $len = $strlen($title);
            $width = $this->getTerminalWidth() ? $this->getTerminalWidth() - 1 : PHP_INT_MAX;
            $lines = array();
            foreach (preg_split("{\r?\n}", $e->getMessage()) as $line) {
                foreach (str_split($line, $width - 4) as $line) {
                    $lines[] = sprintf('  %s  ', $line);
                    $len = max($strlen($line) + 4, $len);
                }
            }

            $messages = array(str_repeat(' ', $len), $title.str_repeat(' ', max(0, $len - $strlen($title))));

            foreach ($lines as $line) {
                $messages[] = $line.str_repeat(' ', $len - $strlen($line));
            }

            $messages[] = str_repeat(' ', $len);

            $output = $this->getErrorOutput();
            $output->writeln("");
            $output->writeln("");
            foreach ($messages as $message) {
                $output->writeln('<error>'.$message.'</error>');
            }
            $output->writeln("");
            $output->writeln("");

            if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                $output->writeln('<comment>Exception trace:</comment>');

                // exception related properties
                $trace = $e->getTrace();
                array_unshift($trace, array(
                    'function' => '',
                    'file'     => $e->getFile() != null ? $e->getFile() : 'n/a',
                    'line'     => $e->getLine() != null ? $e->getLine() : 'n/a',
                    'args'     => array(),
                ));

                for ($i = 0, $count = count($trace); $i < $count; $i++) {
                    $class = isset($trace[$i]['class']) ? $trace[$i]['class'] : '';
                    $type = isset($trace[$i]['type']) ? $trace[$i]['type'] : '';
                    $function = $trace[$i]['function'];
                    $file = isset($trace[$i]['file']) ? $trace[$i]['file'] : 'n/a';
                    $line = isset($trace[$i]['line']) ? $trace[$i]['line'] : 'n/a';

                    $output->writeln(sprintf(' %s%s%s() at <info>%s:%s</info>', $class, $type, $function, $file, $line));
                }

                $output->writeln("");
                $output->writeln("");
            }
        } while ($e = $e->getPrevious());
    }

    /**
     * Tries to figure out the terminal width in which this application runs
     *
     * @return int|null
     */
    protected function getTerminalWidth()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD') && $ansicon = getenv('ANSICON')) {
            return preg_replace('{^(\d+)x.*$}', '$1', $ansicon);
        }

        if (preg_match("{rows.(\d+);.columns.(\d+);}i", exec('stty -a | grep columns'), $match)) {
            return $match[1];
        }
    }

    /**
     * Tries to figure out the terminal height in which this application runs
     *
     * @return int|null
     */
    protected function getTerminalHeight()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD') && $ansicon = getenv('ANSICON')) {
            return preg_replace('{^\d+x\d+ \(\d+x(\d+)\)$}', '$1', trim($ansicon));
        }

        if (preg_match("{rows.(\d+);.columns.(\d+);}i", exec('stty -a | grep columns'), $match)) {
            return $match[2];
        }
    }
}
