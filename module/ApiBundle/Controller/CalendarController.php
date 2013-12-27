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
    Zend\View\Model\ViewModel;

/**
 * CalendarController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class CalendarController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function getActiveEventsAction()
    {
        $items = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $result = array();
        foreach ($items as $item) {
            $result[] = array(
                'title' => $item->getTitle($this->getLanguage()),
                'content' => $item->getContent($this->getLanguage()),
                'startDate' => $item->getStartDate()->format('c'),
                'endDate' => $item->getEndDate() ? $item->getEndDate()->format('c') : null,
                'poster' => $item->getPoster(),
                'location' => $item->getLocation()
            );
        }

        $this->initJson();

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }
}