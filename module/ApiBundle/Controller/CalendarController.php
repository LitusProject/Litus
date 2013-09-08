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

use Zend\View\Model\ViewModel;

/**
 * CalendarController
 *
 * @author Koen Certyn
 */
class CalendarController extends \ApiBundle\Component\Controller\ActionController\ApiController
{

    /**
    * @param \DateTime The startdate of a event
    * @return array
    */
    public function getEvents(DateTime $startDate = null)
    {
        if($startDate == null){
            $items = $this->getEntityManager()
            ->getRepository('CalendarBundle/Entity/Node/Event')
            ->findAll();
        }
        else{
            $items = $this->getEntityManager()
            ->getRepository('CalendarBundle/Entity/Node/Event')
            ->findBy(array('start_date' => $start_date));
        }
        
        
        $result = array();
        foreach ($items as $item) {
            $result[] = array(
                'title' => $item->getTitle($this->getLanguage()),
                'content' => $item->getContent($this->getLanguage()),
                'summary' => $item->getSummary($this->getLanguage()),
                'startDate' => $item->getStartDate()->format('c'),
                'endDate' => $item->getEndDate()->format('c'),
                'poster' => $item-> getPoster(),
                'location' => $item ->getLocation()
            );
        }
        
        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

}