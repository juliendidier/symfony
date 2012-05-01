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
 * The TestOutput could be used for testing commands, to catch and render
 * exceptions.
 *
 * @author Julien DIDIER <julien.didier@sensiolabs.com>
 */
class TestOutput extends ErrorOutput
{
    protected $e;

    /**
     * Constructor.
     *
     * @param integer                  $verbosity The verbosity level (self::VERBOSITY_QUIET, self::VERBOSITY_NORMAL, self::VERBOSITY_VERBOSE)
     * @param Boolean                  $decorated Whether to decorate messages or not (null for auto-guessing)
     * @param OutputFormatterInterface $formatter Output formatter instance
     *
     * @api
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        parent::__construct(fopen('php://memory', 'w'), $verbosity, $decorated, $formatter);
    }

    /**
     * @return OutputInterface
     */
    public function getErrorOutput()
    {
        return $this;
    }

    /**
     * Returns the message exception
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->e->getMessage();
    }

    /**
     * Returns true if statusCode equals to zero
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return 0 == $this->statusCode;
    }

    public function renderException(\Exception $e)
    {
        $this->e = $e;
        parent::renderException($e);
    }
}
