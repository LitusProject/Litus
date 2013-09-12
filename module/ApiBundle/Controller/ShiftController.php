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

    /**
    * Returns all the active shifts by the current person
    *
    * @return array
    */
    public function myShiftAction()
    {
        //TODO key needs to be given and person needs to be get from the key
        //$authenticatedPerson = $key->getPerson();

        //-----DUMMYCODE-----
        $authenticatedPerson = null;
        //---END DUMMYCODE---

        if($this->getAuthentication()->getPersonObject() != null){
            $myShifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($this->getAuthentication()->getPersonObject());
        }
        else{
            return new ViewModel();
        }
        
        
        foreach ($myShifts as $shift) {
            $result[] = array(
                'name' => $shift->getName(),
                'discription' => $shift->getDiscription(),
                'startDate' => $shift->getStartDate(),
                'endDate' => $shift->getEndDate(),
                'manager' => $shift->getManager(), 
                );
        }
        
        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
        
    }

}