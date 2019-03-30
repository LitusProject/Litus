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

namespace PromBundle\Component\Document\Generator\Bus;

use Doctrine\ORM\EntityManager;

/**
 * Csv
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Csv extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param EntityManager $entityManager
     * @param array         $buses
     */
    public function __construct(EntityManager $entityManager, $buses)
    {
        $headers = array('First Name', 'Last Name', 'Code', 'Emailadres', 'Return Bus', 'Return Bus ID');

        $result = array();
        foreach ($buses as $bus) {
            $result[] = array(
                $bus->getName(),
                $bus->getDepartureTime()->format('d/m/Y H:i'),
            );

            $sortedPassengers = $entityManager
                ->getRepository('PromBundle\Entity\Bus\Passenger')
                ->findAllPassengersByBus($bus);

            foreach ($sortedPassengers as $passenger) {
                $returnBus = $passenger->getSecondBus() == null ? '' : $passenger->getSecondBus()->getDepartureTime()->format('d/m/Y H:i');
                $returnBusId = $returnBus == '' ? '' : $passenger->getSecondBus()->getId();

                $result[] = array(
                    $passenger->getFirstName(),
                    $passenger->getLastName(),
                    $passenger->getCode()->getCode(),
                    $passenger->getEmail(),
                    $returnBus,
                    $returnBusId,
                );
            }

            $result[] = array(' ');
        }

        parent::__construct($headers, $result);
    }
}
