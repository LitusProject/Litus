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

namespace CommonBundle\Component\Authentication\Service\ServiceManager;

use CommonBundle\Component\Authentication\Action\Doctrine as DoctrineAction;
use CommonBundle\Component\Authentication\Service\Doctrine as DoctrineService;
use CommonBundle\Entity\User\Session;
use Interop\Container\ContainerInterface;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to instantiate a Doctrine service.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class DoctrineFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return DoctrineService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionStorage = new SessionStorage(
            (getenv('ORGANIZATION') !== false ? getenv('ORGANIZATION') . '_' : '') . 'Litus_Auth'
        );

        $doctrineAction = new DoctrineAction(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get('mail_transport')
        );

        return new DoctrineService(
            $container->get('doctrine.entitymanager.orm_default'),
            Session::class,
            2678400,
            $sessionStorage,
            'Litus_Auth',
            'Session',
            $doctrineAction
        );
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return DoctrineService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'CommonBundle\Component\Authentication\Service\Doctrine');
    }
}
