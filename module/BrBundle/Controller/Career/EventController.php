<?php

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company\Event;
use DateTime;
use Laminas\View\Model\ViewModel;

/**
 * EventController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class EventController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event')
            ->findAllActiveCareerQuery()->getResult();

        return new ViewModel(
            array(
                'events'    => $events,
            )
        );
    }

    public function fetchAction()
    {
        $this->initAjax();

        $events = $this->getEvents();

        if ($events === null) {
            return $this->notFoundAction();
        }

        $result = array();
        foreach ($events as $event) {
            $result[] = array (
                'start' => $event->getStartDate()->getTimeStamp(),
                'end'   => $event->getEndDate()->getTimeStamp(),
                'title' => $event->getTitle(),
                'id'    => $event->getId(),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'events' => (object) $result,
                ),
            )
        );
    }

    public function viewAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'event'    => $event,
                'logoPath' => $logoPath,
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
        foreach ($events as $event) {
            $item = (object) array();
            $item->id = $event->getId();
            $item->poster = $event->getEvent()->getPoster();
            $item->title = $event->getEvent()->getTitle($this->getLanguage());
            $item->companyName = $event->getCompany()->getName();
            // TODO: Localization
            $item->startDate = $event->getEvent()->getStartDate()->format('d/m/Y h:i');
            $item->summary = $event->getEvent()->getSummary(400, $this->getLanguage());
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return array
     */
    private function getEvents()
    {
        if ($this->getParam('start') === null || $this->getParam('end') === null) {
            return;
        }

        $startTime = new DateTime();
        $startTime->setTimeStamp($this->getParam('start'));

        $endTime = new DateTime();
        $endTime->setTimeStamp($this->getParam('end'));

        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event')
            ->findAllByDates($startTime, $endTime);

        if (count($events) == 0) {
            $events = array();
        }

        return $events;
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\Company\Event');

        if (!($event instanceof Event) || $event->getEvent()->getStartDate() < new DateTime() || !$event->getCompany()->isActive()) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'br_career_event',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $event;
    }
}
