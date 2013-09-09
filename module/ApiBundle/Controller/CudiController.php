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
	public function viewAction($authenticatedPerson = null)
    {
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


}