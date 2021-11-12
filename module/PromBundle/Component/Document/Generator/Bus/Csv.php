<?php

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
        $headers = array('First Name', 'Last Name', 'Code', 'Emailadres');

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
                $result[] = array(
                    $passenger->getFirstName(),
                    $passenger->getLastName(),
                    $passenger->getCode()->getCode(),
                    $passenger->getEmail(),
                );
            }

            $result[] = array(' ');
        }

        parent::__construct($headers, $result);
    }
}
