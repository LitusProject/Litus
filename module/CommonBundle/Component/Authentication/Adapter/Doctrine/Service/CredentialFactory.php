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

namespace CommonBundle\Component\Authentication\Adapter\Doctrine\Service;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Credential;
use CommonBundle\Entity\User\Person;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to instantiate a Doctrine credential adapter.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class CredentialFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  array|null         $options
     * @return Credential
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Credential(
            $container->get('doctrine.entitymanager.orm_default'),
            Person::class,
            'username'
        );
    }

    /**
     * @param  ServiceLocatorInterface $locator
     * @return Credential
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'CommonBundle\Component\Authentication\Adapter\Doctrine\Credential');
    }
}
