<?php

namespace CommonBundle\Component\View\Helper;

use CommonBundle\Component\Acl\Driver\HasAccess as HasAccessDriver;

/**
 * A view helper that allows us to easily verify whether or not the authenticated user
 * has access to a resource. This is particularly useful for creating navigation items.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class HasAccess extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * @var HasAccessDriver The driver object
     */
    private $driver = null;

    /**
     * @param  HasAccessDriver $driver The driver object
     * @return self
     */
    public function setDriver(HasAccessDriver $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @param  string $resource The resource that should be verified
     * @param  string $action   The module that should be verified
     * @return boolean
     */
    public function __invoke($resource, $action)
    {
        if ($this->driver === null) {
            throw new Exception\RuntimeException('No driver object was provided');
        }

        $driver = $this->driver;

        return $driver($resource, $action);
    }
}
