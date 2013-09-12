<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
 * @author Koen Certyn
 */

class CudiController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
	
	/**
	* Returns all the bookings and totals of a given user.
	*
	* @param 	$authenticatedPerson
	*			User who's bookings are being searched for.
	*
	* @return 	Array
	*/
	public function viewAction()
    {
        //TODO key needs to be given and person needs to be get from the key
        //$authenticatedPerson = $key->getPerson();

        //-----DUMMYCODE-----
        $authenticatedPerson = null;
        //---END DUMMYCODE---

        if (null === $authenticatedPerson) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

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

    /**
    * Returns the openinghours of Cudi.
    *
    * @return array
    */
    public function weekAction()
    {
        $openingHours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findCurrentWeek();
        echo implode(",", $openingHours);
        $start = new DateTime();
        $start->setTime(0, 0);
        if ($start->format('N') > 5)
            $start->add(new DateInterval('P' . (8 - $start->format('N')) .'D'));
        else
            $start->sub(new DateInterval('P' . ($start->format('N') - 1) .'D'));

        $startHour = 12;
        $endHour = 20;

        $week = array();
        $openingHoursArray = array();
        $start->sub(new DateInterval('P1D'));
        for($i = 0 ; $i < 5 ; $i ++) {
            $start->add(new DateInterval('P1D'));
            $week[] = clone $start;
            $openingHoursArray[$i] = array();
        }

        foreach($openingHours as $openingHour) {
            if ($openingHour->getStart()->format('H') < $startHour)
                $startHour = $openingHour->getStart()->format('H');

            if ($openingHour->getEnd()->format('H') > $endHour)
                $endHour = $openingHour->getEnd()->format('H');

            $openingHoursArray[$openingHour->getStart()->format('N') - 1][] = $openingHour;
        }

        return new ViewModel(
            array(
                'openingHours' => $openingHours,
                'openingHoursTimeline' => $openingHoursArray,
                'week' => $week,
                'startHour' => $startHour,
                'endHour' => $endHour,
            )
        );
    }


}