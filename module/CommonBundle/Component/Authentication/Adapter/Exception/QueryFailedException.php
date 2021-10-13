<?php

namespace CommonBundle\Component\Authentication\Adapter\Exception;

/**
 * This exception is thrown when an error occured while executing the query.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueryFailedException extends \RuntimeException implements \Laminas\Authentication\Exception\ExceptionInterface
{
}
