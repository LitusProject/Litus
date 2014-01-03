<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Controller\Plugin;

use CommonBundle\Component\Acl\Driver\HasAccess as HasAccessDriver;

/**
 * A view helper that allows us to easily verify whether or not the authenticated user
 * has access to a resource. This is particularly useful for creating navigation items.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class HasAccess extends \Zend\Mvc\Controller\Plugin\AbstractPlugin
{
    /**
     * @var \CommonBundle\Component\Acl\Driver\HasAccess The driver object
     */
    private $_helper = null;

    /**
     * @param \CommonBundle\Component\Acl\Helper\HasAccess $acl The driver object
     * @return \CommonBundle\Component\View\Helper\HasAccess
     */
    public function setDriver(HasAccessDriver $driver)
    {
        $this->_driver = $driver;
        return $this;
    }

    /**
     * @param string $resource The resource that should be verified
     * @param string $action The module that should be verified
     * @return bool
     */
    public function __call($resource, $action)
    {
        if (null === $this->_driver)
            throw new Exception\RuntimeException('No driver object was provided');

        return $this->_driver(
            $resource, $action
        );
    }
}
