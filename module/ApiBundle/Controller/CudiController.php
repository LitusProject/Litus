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
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * CudiController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */

class CudiController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function viewAction()
    {
        //TODO key needs to be given and person needs to be get from the key
        //$authenticatedPerson = $key->getPerson();

        //-----DUMMYCODE-----
        $authenticatedPerson = null;
        //---END DUMMYCODE---

        if($authenticatedPerson == null)

            return new ViewModel();

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($authenticatedPerson);

        $total = 0;
        foreach ($bookings as $booking) {
            $total += $booking->getArticle()->getSellPrice();
        }

        return new ViewModel(
            array(
                'bookings' => $bookings,
                'total' => $total,
            )
        );
    }

    public function currentSessionAction()
    {
        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOpen();

        if (sizeof($sessions) >= 1) {
            $result = array(
                'status' => 'open',
                'numberInQueue' => $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                    ->findNbBySession($sessions[0]),
            );
        } else {
            $result = array(
                'status' => 'closed',
            );
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function weekAction()
    {
        $openingHours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findCurrentWeek();

        $result = array();
        foreach ($openingHours as $openingHour) {
            $result[] = array(
                'startDate' => $openingHour->getStart()->format('c'),
                'endDate' => $openingHour->getEnd()->format('c'),
                'comment' => $openingHour->getComment($this->getLanguage()),
            );
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

}
