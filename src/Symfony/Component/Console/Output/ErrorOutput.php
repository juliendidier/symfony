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

/**
 * Output used to render exceptions with style
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Julien DIDIER <julien.didier@sensiolabs.com>
 */
class ErrorOutput extends StreamOutput implements ErrorOutputInterface
{
     /**
     * Renders a catched exception.
     *
     * @param Exception       $e      An exception instance
     */
    public function renderException(\Exception $e)
    {
        $this->setStatusCode($e->getCode());

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
            $width = $this->getTerminalWidth() ? $this->getTerminalWidth() : PHP_INT_MAX;

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

             $this->writeln("");
             $this->writeln("");
             foreach ($messages as $message) {
                 $this->writeln('<error>'.$message.'</error>');
             }
             $this->writeln("");
             $this->writeln("");

             if (OutputInterface::VERBOSITY_VERBOSE === $this->getVerbosity()) {
                 $this->writeln('<comment>Exception trace:</comment>');

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

                     $this->writeln(sprintf(' %s%s%s() at <info>%s:%s</info>', $class, $type, $function, $file, $line));
                 }

                 $this->writeln("");
                 $this->writeln("");
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

        if (preg_match("{rows.(\d+);.columns.(\d+);}i", $this->getSttyColumns(), $match)) {
            return $match[2];
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

        if (preg_match("{rows.(\d+);.columns.(\d+);}i", $this->getSttyColumns(), $match)) {
            return $match[1];
        }
    }

    /**
     * Runs and parses stty -a if it's available, suppressing any error output
     *
     * @return string
     */
    private function getSttyColumns()
    {
        $descriptorspec = array(1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
        $process = proc_open('stty -a | grep columns', $descriptorspec, $pipes, null, null, array('suppress_errors' => true));
        if (is_resource($process)) {
            $info = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            return $info;
        }
    }
}
