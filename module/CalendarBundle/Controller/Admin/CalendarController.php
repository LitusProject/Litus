<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Controller\Admin;

use CalendarBundle\Entity\Node\Event,
    Imagick,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * Handles system admin for calendar.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
                'paginator' => $paginator,
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
                'paginator' => $paginator,
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

    public function editAction()
    {
        if (!($event = $this->getEvent())) {
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
                'form' => $form,
                'event' => $event,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($event = $this->getEvent())) {
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
        if (!($event = $this->getEvent())) {
            return new ViewModel();
        }

        $form = $this->getForm('calendar_event_poster');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'calendar_admin_calendar',
                array(
                    'action' => 'upload',
                    'id' => $event->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'event' => $event,
                'form' => $form,
            )
        );
    }

    private function receive($file, Event $event)
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.poster_path');

        $image = new Imagick($file['tmp_name']);

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
        if (!($event = $this->getEvent())) {
            return new ViewModel();
        }

        $form = $this->getForm('calendar_event_poster');

        if ($this->getRequest()->isPost()) {
            $form->setData(array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            ));

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
                        'info' => array(
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
                        'form' => array(
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
        if (!($event = $this->getEventByPoster())) {
            return new ViewModel();
        }

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.poster_path') . '/';

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => mime_content_type($filePath . $event->getPoster()),
        ));
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
    private function getEvent()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the event!'
            );

            $this->redirect()->toRoute(
                'calendar_admin_calendar',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneById($this->getParam('id'));

        if (null === $event) {
            $this->flashMessenger()->error(
                'Error',
                'No event with the given ID was found!'
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
    private function getEventByPoster()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the event!'
            );

            $this->redirect()->toRoute(
                'calendar_admin_calendar',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneByPoster($this->getParam('id'));

        if (null === $event) {
            $this->flashMessenger()->error(
                'Error',
                'No event with the given ID was found!'
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
