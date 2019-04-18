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

namespace LogisticsBundle\Component\Module;

use LogisticsBundle\Entity\Reservation\Piano as PianoReservation;
use LogisticsBundle\Entity\Reservation\Resource;
use LogisticsBundle\Entity\Reservation\Van as VanReservation;

/**
 * LogisticsBundle installer
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Installer extends \CommonBundle\Component\Module\AbstractInstaller
{
    protected function postInstall()
    {
        $this->write('Installing resources...');
        $this->installResources();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
    }

    private function installResources()
    {
        $resources = array(
            VanReservation::RESOURCE_NAME,
            PianoReservation::RESOURCE_NAME
        );

        foreach ($resources as $name) {
            $resource = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\Resource')
                ->findOneByName($name);

            if ($resource == null) {
                $this->getEntityManager()->persist(new Resource($name));
            }
        }
    }
}
