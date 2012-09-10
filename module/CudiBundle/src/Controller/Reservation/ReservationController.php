<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Reservation;

use Zend\View\Model\ViewModel;

/**
 * ReservationController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ReservationController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $authenticatedPerson = $this->getAuthentication()->getPersonObject();
        
        if (null === $authenticatedPerson) {
            return new ViewModel();
        }
        
        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($authenticatedPerson);
        
        return new ViewModel(
            array(
                'bookings' => $bookings,
            )
        );
    }
}