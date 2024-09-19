<?php

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
            PianoReservation::RESOURCE_NAME,
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
