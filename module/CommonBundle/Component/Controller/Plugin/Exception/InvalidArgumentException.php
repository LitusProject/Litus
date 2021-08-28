<?php

namespace CommonBundle\Component\Controller\Plugin\Exception;

/**
 * Custom InvalidArgumentException so that we can quickly see where it was
 * thrown.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InvalidArgumentException extends \InvalidArgumentException
{
}
