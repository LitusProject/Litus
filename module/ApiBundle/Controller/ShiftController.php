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

namespace ApiBundle\Controller;

use DateInterval,
    DateTime,
    IntlDateFormatter,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * @author Koen Certyn
 */
class ShiftController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function getActiveAction()
    {
        $this->initJson();

        $authenticatedPerson = $this->getAccessToken()->getPerson($this->getEntityManager());
        if (null === $authenticatedPerson)
            return $this->error(401, '');

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($authenticatedPerson);

        $result = array();
        foreach ($shifts as $shift) {
            $result[] = array(
                'id'                    => $shift->getId(),

                'canHaveAsResponsible'  => $shift->canHaveAsResponsible($this->getEntityManager(), $authenticatedPerson),
                'canHaveAsVolunteer'    => $shift->canHaveAsVolunteer($this->getEntityManager(), $authenticatedPerson),
                'description'           => $shift->getDescription(),
                'currentNbResponsibles' => count($shift->getResponsibles()),
                'currentNbVolunteers'   => count($shift->getVolunteers()),
                'endDate'               => $shift->getEndDate()->format('c'),
                'signedUp'              => true,
                'manager'               => $shift->getManager()->getFullName(),
                'name'                  => $shift->getName(),
                'nbResponsibles'        => $shift->getNbResponsibles(),
                'nbVolunteers'          => $shift->getNbVolunteers(),
                'startDate'             => $shift->getStartDate()->format('c'),

                'location'              => array(
                    'id'        => $shift->getLocation()->getId(),
                    'latitude'  => $shift->getLocation()->getLatitude(),
                    'longitude' => $shift->getLocation()->getLongitude(),
                    'name'      => $shift->getLocation()->getName(),
                ),
                'unit'                  => array(
                    'id'   => $shift->getUnit()->getId(),
                    'name' => $shift->getUnit()->getName(),
                ),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );

    }

}
