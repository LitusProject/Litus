<?php

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Event;
use BrBundle\Entity\Company;
use CommonBundle\Entity\User\Person\Academic;
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
                'events' => $events,
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

        error_log($event->getTitle());

        return new ViewModel(
            array(
                'event'    => $event,
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


    public function subscribeAction()
    {
        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
        } else {
            $person = null;
        }

        $form = $this->getForm('br_career_event_subscription_add');

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }
        $form->setEvent($event);

        if ($person instanceof Academic) {
            
            //TODO: Check for double subscriptions??

            $data = array();
            $data['first_name'] = $person->getFirstName();
            $data['last_name'] = $person->getLastName();
            $form->setData($data);
        }
        

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'You have succesfully subscribed for this event!'
                );

                $this->redirect()->toRoute(
                    'br_career_event',
                    array(
                        'action' => 'view',
                        'id'  => $event->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        

        return new ViewModel(
            array(
                'event'=> $event,
                'form' => $form,
            )
        );
    }


    public function mapAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $attendingCompaniesMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\CompanyMap')
            ->findAllByEventQuery($event)
            ->getResult();

        $interestedMasters = array();
        foreach ($attendingCompaniesMaps as $companyMap){
            $interestedMasters[$companyMap->getCompany()->getId()] = $companyMap->getMasterInterests();
        }

        $locations = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\Location')
            ->findAllByEventQuery($event)
            ->getResult();


        
        return new ViewModel(
            array(
                'event'                 => $event,
                'locations'             => $locations,
                'attendingCompanies'    => $attendingCompaniesMaps,
                'interestedMasters'     => $interestedMasters,
                'masters'               => Company::POSSIBLE_MASTERS + array('other' => 'Other')
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
        $event = $this->getEntityById('BrBundle\Entity\Event');

        if (!($event instanceof Event)) {
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
