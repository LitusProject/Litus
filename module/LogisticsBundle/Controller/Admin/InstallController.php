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

namespace LogisticsBundle\Controller\Admin;

use Exception,
    LogisticsBundle\Entity\Reservation\VanReservation,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    LogisticsBundle\Entity\Reservation\ReservableResource;

/**
 * InstallController for the LogisticsBundle
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function postInstall()
    {
        $this->_installResources();
    }

    private function _installResources()
    {
        $resources = array(VanReservation::VAN_RESOURCE_NAME, PianoReservation::PIANO_RESOURCE_NAME);

        foreach ($resources as $name) {
            $resource = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                ->findOneByName($name);

            if (null == $resource) {
                $this->getEntityManager()->persist(new ReservableResource($name));
            }
        }
    }
}
