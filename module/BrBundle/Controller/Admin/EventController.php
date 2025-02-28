<?php

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Event;
use BrBundle\Entity\Event\CompanyMap;
use DateInterval;
use Laminas\View\Model\ViewModel;

/**
 * EventController
 *
 * Controller for events organised by VTK Corporate Relations itself.
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */

class EventController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person == null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_event_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The event was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $propertiesForm = $this->getForm('br_event_edit', array('event' => $event));
        $companyMapForm = $this->getForm('br_event_companyMap', array('event' => $event));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $propertiesForm->setData($formData);
            $companyMapForm->setData($formData);

            if (isset($formData['event_edit']) && $propertiesForm->isValid()) {
                $this->getEntityManager()->persist(
                    $propertiesForm->hydrateObject($event)
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The event was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event',
                    array(
                        'action' => 'manage',
                    )
                );
            } elseif (isset($formData['event_companyMap']) && $companyMapForm->isValid()) {
                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']);
                if ($this->getEntityManager()                    ->getRepository('BrBundle\Entity\Event\CompanyMap')                    ->findByEventAndCompany($event, $company) != null
                ) {
                    $objectMap = new CompanyMap($company, $event);
                    $objectMap->setDone();
                    $this->getEntityManager()->persist($objectMap);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The attendee was successfully added!'
                    );
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'That company is already attending this event!'
                    );
                }

                $this->redirect()->toRoute(
                    'br_admin_event',
                    array(
                        'action' => 'edit',
                        'id'     => $event->getId(),
                    )
                );
            }
        }


        $allEventCompanyMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\CompanyMap')
            ->findAllByEvent($event);

        $maps = array();
        $comps = array();

        foreach ($allEventCompanyMaps as $map) {
            $comp = $map->getCompany()->getId();
            if (!in_array($comp, $comps)) {
                array_push($comps, $comp);
                array_push($maps, $map);
            }
        }

        return new ViewModel(
            array(
                'propertiesForm'   => $propertiesForm,
                'companyMapForm'   => $companyMapForm,
                'eventCompanyMaps' => $maps,
                'event'            => $event,
            )
        );
    }

    public function statisticsAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $repository = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\Visitor');

        $interval = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.event_graph_interval')
        );

        $logGraphData = array(
            'expirationTime' => $event->getEndDate(),
            'labels'         => array(),
            'dataset'        => array(),
        );



        $sortedVisitors = $repository->findSortedByEvent($event);

        if ($sortedVisitors != null) {
            $time = clone $event->getStartDate();
            $endTime = $event->getEndDate();
            // $endInterval = clone $time;
            // $endInterval->add($interval);

            while ($time <= $endTime) {
                $result = $repository->countAtTimeByEvent($event, $time);
                // $result = $repository->countBetweenByEvent($event, $time, $endInterval);
                $logGraphData['labels'][] = $time->format('d/m H:i');
                $logGraphData['dataset'][] = ($result ? $result[0][1] : 0);
                $time->add($interval);
                // $endInterval->add($interval);
            }
        }

        $subscribersCount = count(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\Subscription')
                ->findAllByEventQuery($event)
                ->getResult()
        );

        $uniqueVisitors = $repository->countUniqueByEvent($event);

        $currentVisitors = count($repository->findCurrentVisitors($event));

        $maps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\CompanyMap')
            ->findAllByEventQuery($event)
            ->getResult();


        $attendees = 0;
        foreach ($maps as $map) {
            $attendees += $map->getAttendees();
        }

        $matchesCount = count(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\Connection')
                ->findAllByEvent($event)
        );


        $totals = array(
            'visitors'        => $sortedVisitors ? $uniqueVisitors[0][1] : 0,
            'subscribers'     => $subscribersCount,
            'current'         => $currentVisitors,
            'representatives' => $attendees,
            'matches'         => $matchesCount,
        );



        return new ViewModel(
            array(
                'event'    => $event,
                'logGraph' => $logGraphData,
                'totals'   => $totals,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteAttendeeAction()
    {
        $this->initAjax();


        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $companymap = $this->getCompanyMapEntity();
        if ($companymap === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($companymap);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function editAttendeeAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $companyMap = $this->getCompanyMapEntity();

        $form = $this->getForm('br_event_company_edit', array('companyMap' => $companyMap ));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if ($form->isValid()) {
                error_log('here');
                $this->getEntityManager()->persist(
                    $form->hydrateObject($companyMap)
                );
                $this->getEntityManager()->flush();

                error_log($event->getId());

                $this->flashMessenger()->success(
                    'Success',
                    'The attending company was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event',
                    array(
                        'action' => 'edit',
                        'id'     => $event->getId(),
                    )
                );
                return new ViewModel(
                    array(
                        'event' => $event,
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form'  => $form,
                'event' => $event,
                'map'   => $companyMap,
            )
        );
    }

    public function guideAction()
    {
        $event = $this->getEventEntity();

        if ($event === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_admin_event_guide');

        if ($this->getRequest()->isPost()) {
            $formData = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($formData);

            if ($form->isValid()) {
                $filePath = 'public' . $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('publication.public_pdf_directory');

                do {
                    $fileName = sha1(uniqid()) . '.pdf';
                } while (file_exists($filePath . $fileName));

                $event->setGuide($fileName);

                rename($formData['file']['tmp_name'], $filePath . $fileName);

                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The Company Guide was succesfully uploaded!'
                );
            }
        }

        return new ViewModel(
            array(
                'event' => $event,
                'form'  => $form,
            ),
        );
    }

    public function busschemaAction()
    {
        $event = $this->getEventEntity();

        if ($event === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_admin_event_busschema');

        if ($this->getRequest()->isPost()) {
            $formData = array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($formData);

            if ($form->isValid()) {
                $filePath = 'public' . $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('publication.public_pdf_directory');

                do {
                    $fileName = sha1(uniqid()) . '.pdf';
                } while (file_exists($filePath . $fileName));

                $event->setBusschema($fileName);

                rename($formData['file']['tmp_name'], $filePath . $fileName);

                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The Timetable was succesfully uploaded!'
                );
            }
        }

        return new ViewModel(
            array(
                'event' => $event,
                'form'  => $form,
            )
        );
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
                'br_admin_event',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }

    /**
     * @return CompanyMap|null
     */
    private function getCompanyMapEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\Event\CompanyMap', 'map');
        if (!($event instanceof CompanyMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No Map was found!'
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
