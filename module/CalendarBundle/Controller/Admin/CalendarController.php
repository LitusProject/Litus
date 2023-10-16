<?php

namespace CalendarBundle\Controller\Admin;

use CalendarBundle\Entity\Node\Event;
use Imagick;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * Handles system admin for calendar.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class CalendarController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CalendarBundle\Entity\Node\Event')
                ->findAllActiveQuery(0),
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
                ->getRepository('CalendarBundle\Entity\Node\Event')
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
        $form = $this->getForm('calendar_event_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $event = $form->hydrateObject();

                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The event was successfully added!'
                );

                $this->redirect()->toRoute(
                    'calendar_admin_calendar',
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

    public function templateAction()
    {
        $form = $this->getForm('calendar_event_add');
        $shiftForm = $this->getForm('shift_shift_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $event = $form->hydrateObject();

                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $data = array(
                    'manager'               => false,
                    'unit'                  => 3,
                    'event'                 => $event->getId(),
                    'location'              => 2,
                    'nb_responsibles'       => 0,
                    'handled_on_event'      => false,
                    'ticket_needed'         => false,
                    'points'                => 0,
                );

                $startDate = $event->getStartDate()->format('d/m/Y');
                $endDate = $event->getEndDate()->format('d/m/Y');

                $data['name'] = 'Pispolitie';
                $data['description'] = 'Mensen die naar het toilet willen een strafje geven';
                $data['nb_volunteers'] = 2;
                $data['nb_volunteers_min'] = 2;
                $data['reward'] = 2;
                $data['start_date'] = $startDate . ' 20:30';
                $data['end_date'] = $startDate . ' 22:00';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['start_date'] = $startDate . ' 22:15';
                $data['end_date'] = $endDate . ' 00:00';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['name'] = 'Controleren QR-code + bandjes uitdelen';
                $data['description'] = 'Controleren QR-code + bandjes uitdelen';
                $data['reward'] = 1;
                $data['start_date'] = $startDate . ' 20:00';
                $data['end_date'] = $startDate . ' 20:30';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['name'] = 'Stewarden';
                $data['description'] = 'Zorgen dat er niemand luid is buiten en fiksen dat er geen drank buiten geraakt';
                $data['start_date'] = $startDate . ' 22:00';
                $data['end_date'] = $startDate . ' 22:15';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['start_date'] = $endDate . ' 00:00';
                $data['end_date'] = $endDate . ' 00:15';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['name'] = 'Bijrijden';
                $data['description'] = 'Chille in de kar. Locatie van de loods is: Tervuursevest 238';
                $data['nb_volunteers_min'] = 1;
                $data['start_date'] = $startDate . ' 18:00';
                $data['end_date'] = $startDate . ' 19:00';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['name'] = 'Corona stilhouden';
                $data['description'] = 'Je mag ze ook buiten gooien als ze niet stil zijn xxx';
                $data['start_date'] = $endDate . ' 00:15';
                $data['end_date'] = $endDate . ' 01:00';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['name'] = 'Bandjes controleren';
                $data['description'] = 'Bandjes controleren tijdens de tempus';
                $data['nb_volunteers'] = 1;
                $data['start_date'] = $startDate . ' 22:00';
                $data['end_date'] = $startDate . ' 22:15';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['start_date'] = $endDate . ' 00:00';
                $data['end_date'] = $endDate . ' 00:15';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['name'] = 'Tappen & Rondbrengen';
                $data['description'] = 'Tappen & Rondbrengen';
                $data['nb_volunteers'] = 4;
                $data['nb_volunteers_min'] = 3;
                $data['reward'] = 2;
                $data['start_date'] = $startDate . ' 20:30';
                $data['end_date'] = $startDate . ' 22:00';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['start_date'] = $startDate . ' 22:15';
                $data['end_date'] = $endDate . ' 00:00';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['start_date'] = $endDate . ' 00:15';
                $data['end_date'] = $endDate . ' 01:00';
                $data['reward'] = 1;

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['name'] = 'Opbouwen';
                $data['description'] = 'Wat tafels en stoelen klaar zetten voor de cantus';
                $data['start_date'] = $startDate . ' 19:00';
                $data['end_date'] = $startDate . ' 20:00';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $data['name'] = 'Afbraak';
                $data['description'] = 'Please help ons mee en zorg dat we na een half uurtje klaar kunnen zijn :))';
                $data['nb_volunteers'] = 5;
                $data['start_date'] = $endDate . ' 01:00';
                $data['end_date'] = $endDate . ' 02:00';

                $this->getEntityManager()->persist(
                    $shiftForm->getHydrator()->hydrate($data)
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The event and shifts were successfully added!'
                );

                $this->redirect()->toRoute(
                    'calendar_admin_calendar',
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

        $form = $this->getForm('calendar_event_edit', array('event' => $event));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The event was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'calendar_admin_calendar',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'  => $form,
                'event' => $event,
                'em'    => $this->getEntityManager(),
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

        $event->setIsHistory(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function editPosterAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $form = $this->getForm('calendar_event_poster');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'calendar_admin_calendar',
                array(
                    'action' => 'upload',
                    'id'     => $event->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'event' => $event,
                'form'  => $form,
            )
        );
    }

    private function receive($file, Event $event)
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.poster_path');

        $image = new Imagick($file['tmp_name']);
        $image->thumbnailImage(600, 400, true);

        if ($event->getPoster() != '' || $event->getPoster() !== null) {
            $fileName = '/' . $event->getPoster();
        } else {
            do {
                $fileName = '/' . sha1(uniqid());
            } while (file_exists($filePath . $fileName));
        }

        $image->writeImage($filePath . $fileName);

        $event->setPoster($fileName);
    }

    public function uploadAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $form = $this->getForm('calendar_event_poster');

        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($form->isValid()) {
                $formData = $form->getData();

                $this->receive($formData['poster'], $event);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The event\'s poster has successfully been updated!'
                );

                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info'   => array(
                            'info' => array(
                                'name' => $event->getPoster(),
                            ),
                        ),
                    )
                );
            } else {
                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form'   => array(
                            'errors' => $form->getMessages(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'status' => 'error',
            )
        );
    }

    public function posterAction()
    {
        $event = $this->getEventEntityByPoster();
        if ($event === null) {
            return new ViewModel();
        }

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.poster_path') . '/';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Type' => mime_content_type($filePath . $event->getPoster()),
            )
        );
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath . $event->getPoster(), 'r');
        $data = fread($handle, filesize($filePath . $event->getPoster()));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('CalendarBundle\Entity\Node\Event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'calendar_admin_calendar',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }

    /**
     * @return Event|null
     */
    private function getEventEntityByPoster()
    {
        $event = $this->getEntityById('CalendarBundle\Entity\Node\Event', 'id', 'poster');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'calendar_admin_calendar',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }
}
