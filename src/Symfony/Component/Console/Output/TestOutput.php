<?php

namespace Symfony\Component\Console\Output;

class TestOutput extends ErrorOutput
{
    /**
     * @new
     */
    protected $e;

    /**
     * @new
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

    public function getErrorMessage()
    {
        return $this->e->getMessage();
    }

    /**
     * @new
     */
    public function isSuccessful()
    {
        return 0 == $this->statusCode;
    }

    /**
     * @new
     */
    public function renderException(\Exception $e)
    {
        $this->e = $e;
        parent::renderException($e);
    }
}
