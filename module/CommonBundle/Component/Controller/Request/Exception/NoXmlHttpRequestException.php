<?php

namespace CommonBundle\Component\Controller\Request\Exception;

/**
 * Thrown when someone tries to access an AJAX action directly.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NoXmlHttpRequestException extends \RuntimeException
{
}
