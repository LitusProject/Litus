<?php


namespace BrBundle\Controller\Admin\Event;


use BrBundle\Entity\Event;
use BrBundle\Entity\Event\Location;
use Laminas\View\Model\ViewModel;

class LocationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\Location')
                ->findAllByEventQuery($event),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'event'             => $event,
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('br_event_location_add');
        $eventObject = $this->getEventEntity();
        // $form->setEvent($eventObject);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            
            if ($form->isValid()) {
                $location = $form->hydrateObject();
                $location->setEvent($eventObject);
                $this->getEntityManager()->persist(
                    $location
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The Subscription was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event_location',
                    array(
                        'action' => 'manage',
                        'event'  => $eventObject->getId(),
                    )
                );

                return new ViewModel(array(
                    'event' => $eventObject,
                ));
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'event' => $eventObject,
            )
        );
    }


    public function editAction(){
        $location = $this->getLocationEntity();

        $form = $this->getForm('br_event_location_edit', $location);
        $eventObject = $this->getEventEntity();
        // $form->setEvent($eventObject);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            
            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The Subscription was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event_location',
                    array(
                        'action' => 'manage',
                        'event'  => $eventObject->getId(),
                    )
                );

                return new ViewModel(array(
                    'event' => $eventObject,
                ));
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'event' => $eventObject,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $location = $this->getLocationEntity();
        if ($location === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($location);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }


    /**
     * @return Location|null
     */
    private function getLocationEntity()
    {
        $location = $this->getEntityById('BrBundle\Entity\Event\Location');

        if (!($location instanceof Location)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_event_location',
                array(
                    'action' => 'manage',
                    'event'  => $this->getEventEntity()->getId(),
                )
            );

            return;
        }

        return $location;
    }


    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\Event', 'event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_event',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }
}