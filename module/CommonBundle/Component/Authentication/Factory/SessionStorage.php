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

namespace CommonBundle\Component\Authentication\Factory;

use Zend\Authentication\Storage\Session as SessionStorageObject,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create a session storage container for authentication.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class SessionStorage implements \Zend\ServiceManager\FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        return new SessionStorageObject(getenv('ORGANIZATION') . '_Litus_Auth');
    }
}
