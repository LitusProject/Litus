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

namespace CommonBundle\Component\Authentication\Factory\Adapter;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Credential as DoctrineAdapter,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create a Doctrine Adapter.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Doctrine implements \Zend\ServiceManager\FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        return new DoctrineAdapter(
            $sl->get('doctrine.entitymanager.orm_default'),
            'CommonBundle\Entity\User\Person',
            'username'
        );
    }
}
