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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Authentication\Factory\Service;

use CommonBundle\Component\Authentication\Service\Doctrine as DoctrineService,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create a Doctrine Service.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Doctrine implements \Zend\ServiceManager\FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        return new DoctrineService(
            $sl->get('doctrine.entitymanager.orm_default'),
            'CommonBundle\Entity\User\Session',
            2678400,
            $sl->get('authentication_sessionstorage'),
            'Litus_Auth',
            'Session',
            $sl->get('authentication_action')
        );
    }
}
