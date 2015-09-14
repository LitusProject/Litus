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

namespace CommonBundle\Component\Module\Factory;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Session\Container as SessionContainer;

/**
 * Factory to create a session storage container.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class SessionStorage implements \Zend\ServiceManager\FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        return new SessionContainer(getenv('ORGANIZATION') . '_Litus_Common');
    }
}
