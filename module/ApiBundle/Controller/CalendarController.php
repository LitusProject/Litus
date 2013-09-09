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
 * CalendarController
 *
 * @author Koen Certyn
 */
class CalendarController extends \ApiBundle\Component\Controller\ActionController\ApiController
{

    /**
    * @return array
    */
    
    public function getEventAction()
    {
        $date = date("Y-m-d H:i:s");
        echo $date;
        $first = DateTime::createFromFormat('d-m-Y H:i', '1-' . $date . ' 0:00');
        echo $first;
        $last = strtotime("+60 days", $date);
        echo "qsdfqsdfsfd     ".$last;
        $items = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllBetween($date, $date);
        
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