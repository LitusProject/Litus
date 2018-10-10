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

namespace CommonBundle\Component\Controller\Plugin\Service;

use CommonBundle\Component\Controller\Plugin\Paginator,
    CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface,
    Interop\Container\ContainerInterface,
    Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to instantiate a paginator.
 * 
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class PaginatorFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Paginator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $paginator = new Paginator();
        $paginator->setServiceLocator($container);

        return $paginator;
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return Paginator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Paginator::class);
    }
}
