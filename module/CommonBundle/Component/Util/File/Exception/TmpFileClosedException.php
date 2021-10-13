<?php

namespace CommonBundle\Component\Util\File\Exception;

/**
 * This exception is thrown when a handle to a tempory file, that is already closed,
 * is used.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class TmpFileClosedException extends \Exception
{
}
