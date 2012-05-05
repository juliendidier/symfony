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
 * Represents the interface that all ErrorOutput must implement.
 *
 * This interface brings the exception rendering
 *
 * @see OutputInterface
 *
 * @author Julien DIDIER <julien.didier@sensiolabs.com>
 */
interface ErrorOutputInterface extends OutputInterface
{
    /**
     * Renders a catched exception.
     *
     * @param Exception       $e      An exception instance
     */
    function renderException(\Exception $e);
}
