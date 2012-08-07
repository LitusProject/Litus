<?php

namespace CalendarBundle\Controller;

use DateTime;

/**
 * Handles system calendar controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CalendarController extends \CommonBundle\Component\Controller\ActionController\CommonController
{
    public function overviewAction()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
            ->findAllActive();
                
        $ordered = array();
        foreach($events as $event) {
            $key = $event->getStartDate()->format('Y_m');
            if (!isset($ordered[$key])) {
                $ordered[$key] = (object) array(
                    'events' => array(),
                    'date' => DateTime::createFromFormat('Y_m', $key)
                );
            }
                        
            $ordered[$key]->events[] = $event;
        }

        return array(
            'events' => $ordered,
        );
    }
    
    public function viewAction()
    {
        if (!($event = $this->_getTranslationByName()))
            return;
            
        return array(
            'event' => $event,
        );
    }
    
    public function _getTranslationByName()
    {
        if (null === $this->getParam('name')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    
        $translation = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Translation')
            ->findOneByName($this->getParam('name'));
        
        if (null === $translation || $translation->getLanguage() != $this->getLanguage()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        return $translation;
    }
}
