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
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Career;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    \DateTime,
    Zend\View\Model\ViewModel;

/**
 * EventController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class EventController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Event')
            ->findAllFuture(new DateTime()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function viewAction()
    {
        $event = $this->_getEvent();

        return new ViewModel(
            array(
                'event' => $event,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Event')
            ->findAllFutureBySearch(new DateTime(), $this->getParam('string'));

        $result = array();
        foreach($events as $event) {
            $item = (object) array();
            $item->id = $event->getId();
            $item->poster = $event->getEvent()->getPoster();
            $item->title = $event->getEvent()->getTitle($this->getLanguage());
            $item->companyName = $event->getCompany()->getName();
            $item->startDate = $event->getEvent()->getStartDate()->format('d/m/Y h:i'); // TODO localized
            $item->summary = $event->getEvent()->getSummary(400, $this->getLanguage());
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _getEvent()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the event!'
                )
            );

            $this->redirect()->toRoute(
                'br_career_event',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Event')
            ->findOneActiveById($this->getParam('id'));

        if (null === $event) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No event with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_career_event',
                array(
                    'action' => 'overview'
                )
            );

            return;
        }

        return $event;
    }
}
