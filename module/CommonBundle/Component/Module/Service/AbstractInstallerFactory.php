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

namespace CommonBundle\Component\Module\Service;

use CommonBundle\Component\Module\AbstractInstaller;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Abstract factory instantiating an installer.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AbstractInstallerFactory implements AbstractFactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @return boolean
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (class_exists($requestedName)) {
            return in_array(AbstractInstaller::class, class_parents($requestedName), true);
        }

        return false;
    }

    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return AbstractInstaller
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $installer = new $requestedName();
        if ($installer instanceof ServiceLocatorAwareInterface) {
            $installer->setServiceLocator($container);
        }

        return $installer;
    }
}
