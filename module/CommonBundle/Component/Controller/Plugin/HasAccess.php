<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Controller\Plugin;

use CommonBundle\Component\Acl\Driver\HasAccess as HasAccessDriver;
use RuntimeException;

/**
 * A view helper that allows us to easily verify whether or not the authenticated user
 * has access to a resource. This is particularly useful for creating navigation items.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class HasAccess extends \Zend\Mvc\Controller\Plugin\AbstractPlugin
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
    public function toResourceAction($resource, $action)
    {
        if ($this->driver === null) {
            throw new RuntimeException('No driver object was provided');
        }

        $driver = $this->driver;

        return $driver($resource, $action);
    }
}
