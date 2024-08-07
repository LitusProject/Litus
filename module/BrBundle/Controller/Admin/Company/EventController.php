<?php

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company;
use BrBundle\Entity\Company\Event;
use Imagick;
use Laminas\View\Model\ViewModel;

/**
 * EventController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EventController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Event')
                ->findAllByCompanyQuery($company),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'company'           => $company,
            )
        );
    }

    public function addAction()
    {
        $company = $this->getCompanyEntity();
        if ($company === null) {
            return new ViewModel();
        }

        $form = $this->getForm('calendar_event_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $commonEvent = $form->hydrateObject();
                $this->getEntityManager()->persist($commonEvent);

                $event = new Event(
                    $commonEvent,
                    $company
                );

                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The event was successfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_company_event',
                    array(
                        'action' => 'manage',
                        'id'     => $company->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $company,
                'form'    => $form,
            )
        );
    }

    public function editAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $form = $this->getForm('calendar_event_edit', array('event' => $event->getEvent()));

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
                    'br_admin_company_event',
                    array(
                        'action' => 'manage',
                        'id'     => $event->getCompany()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $event->getCompany(),
                'form'    => $form,
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
                'br_admin_company_event',
                array(
                    'action' => 'upload',
                    'id'     => $event->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'event' => $event->getEvent(),
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

        if ($event->getEvent()->getPoster() != '' || $event->getEvent()->getPoster() !== null) {
            $fileName = '/' . $event->getEvent()->getPoster();
        } else {
            do {
                $fileName = '/' . sha1(uniqid());
            } while (file_exists($filePath . $fileName));
        }

        $image->writeImage($filePath . $fileName);

        $event->getEvent()->setPoster($fileName);
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
                                'name' => $event->getEvent()->getPoster(),
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

    /**
     * @return Company|null
     */
    private function getCompanyEntity()
    {
        $company = $this->getEntityById('BrBundle\Entity\Company');

        if (!($company instanceof Company)) {
            $this->flashMessenger()->error(
                'Error',
                'No company was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $company;
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\Company\Event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }
}
