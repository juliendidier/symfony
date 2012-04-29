<?php

namespace Symfony\Component\Console\Output;

interface ErrorOutputInterface extends OutputInterface
{
    /**
     * @new
     */
    function renderException(\Exception $e);
}
