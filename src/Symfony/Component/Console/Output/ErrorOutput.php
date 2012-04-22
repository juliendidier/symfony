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

class ErrorOutput extends Output
{
    private $stream;

    /**
     * @new
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        $this->stream = fopen('php://stderr', 'w');

        if (null === $decorated) {
            $decorated = $this->hasColorSupport($decorated);
        }

        parent::__construct($verbosity, $decorated, $formatter);
    }

    /**
     * Gets the stream attached to this StreamOutput instance.
     *
     * @return resource A stream resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Writes a message to the output.
     *
     * @param string  $message A message to write to the output
     * @param Boolean $newline Whether to add a newline or not
     *
     * @throws \RuntimeException When unable to write output (should never happen)
     */
    public function doWrite($message, $newline)
    {
        if (false === @fwrite($this->stream, $message.($newline ? PHP_EOL : ''))) {
            // @codeCoverageIgnoreStart
            // should never happen
            throw new \RuntimeException('Unable to write output.');
            // @codeCoverageIgnoreEnd
        }

        fflush($this->stream);
    }

    /**
     * @new
     */
    public function getDisplay()
    {
        rewind($this->getStream());

        return stream_get_contents($this->getStream());
    }

    /**
     * Returns true if the stream supports colorization.
     *
     * Colorization is disabled if not supported by the stream:
     *
     *  -  windows without ansicon
     *  -  non tty consoles
     *
     * @return Boolean true if the stream supports colorization, false otherwise
     */
    protected function hasColorSupport()
    {
        // @codeCoverageIgnoreStart
        if (DIRECTORY_SEPARATOR == '\\') {
            return false !== getenv('ANSICON');
        }

        return function_exists('posix_isatty') && @posix_isatty($this->stream);
        // @codeCoverageIgnoreEnd
    }
}
