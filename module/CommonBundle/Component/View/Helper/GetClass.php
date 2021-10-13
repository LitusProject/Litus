<?php

namespace CommonBundle\Component\View\Helper;

/**
 * A view helper that calls PHP's get_class.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class GetClass extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * @param  object $object
     * @return string
     */
    public function __invoke(object $object)
    {
        // phpcs:disable SlevomatCodingStandard.Classes.ModernClassNameReference.ClassNameReferencedViaFunctionCall
        return get_class($object);
        // phpcs:enable
    }
}
