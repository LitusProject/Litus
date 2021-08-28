<?php

namespace CommonBundle\Component\Util\File\Exception;

/**
 * This exception is thrown when a handle to a tempory file, that is already closed,
 * is used.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FailedToOpenException extends \Exception
{
}
