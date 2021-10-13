<?php

namespace CommonBundle\Component\Controller\Exception;

/**
 * Thrown when someone tries to access a resource that he or she does not
 * have access to.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class HasNoAccessException extends \RuntimeException
{
}
