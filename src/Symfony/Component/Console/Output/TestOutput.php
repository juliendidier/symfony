<?php

namespace Symfony\Component\Console\Output;

class TestOutput extends StreamOutput
{
    protected $e;
    protected $statusCode;

    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        parent::__construct(fopen('php://memory', 'w'), $verbosity, $decorated, $formatter);
    }

    public function getErrorOutput()
    {
        return $this->stream;
    }

    public function renderException(\Exception $e)
    {
        $this->e = $e;
    }

    public function getErrorMessage()
    {
        return $this->e->getMessage();
    }
    /**
     * @new
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @new
     */
    public function SetStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @new
     */
    public function isSuccessful()
    {
        return 0 == $this->statusCode;
    }

}
