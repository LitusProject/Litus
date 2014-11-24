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

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company\Event,
    Imagick,
    Zend\View\Model\ViewModel;

/**
 * EventController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EventController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($company = $this->_getCompany())) {
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
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'company' => $company,
            )
        );
    }

    public function addAction()
    {
        if (!($company = $this->_getCompany())) {
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
                        'id' => $company->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $company,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($event = $this->_getEvent())) {
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
                        'id' => $event->getCompany()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'company' => $event->getCompany(),
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($event = $this->_getEvent())) {
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
        if (!($event = $this->_getEvent())) {
            return new ViewModel();
        }

        $form = $this->getForm('calendar_event_poster');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'br_admin_company_event',
                array(
                    'action' => 'upload',
                    'id' => $event->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'event' => $event->getEvent(),
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
        if (!($event = $this->_getEvent())) {
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
                                'name' => $fileName,
                            ),
                        ),
                    )
                );
            } else {
                $formErrors = array();

                if (sizeof($upload->getMessages()) > 0) {
                    $formErrors['poster'] = $upload->getMessages();
                }

                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form' => array(
                            'errors' => $formErrors,
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
     * @return BrBundle\Entity\Company|null
     */
    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the company!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->error(
                'Error',
                'No company with the given ID was found!'
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
     * @return \BrBundle\Entity\Company\Event|null
     */
    private function _getEvent()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the event!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Event')
            ->findOneById($this->getParam('id'));

        if (null === $event) {
            $this->flashMessenger()->error(
                'Error',
                'No event with the given ID was found!'
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
