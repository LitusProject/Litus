<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company\Event,
    CalendarBundle\Form\Admin\Event\Add as AddForm,
    CalendarBundle\Form\Admin\Event\Edit as EditForm,
    CalendarBundle\Form\Admin\Event\Poster as PosterForm,
    CalendarBundle\Entity\Node\Event as CommonEvent,
    CalendarBundle\Entity\Node\Translation,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    Imagick,
    Zend\Http\Headers,
    Zend\File\Transfer\Transfer as FileTransfer,
    Zend\Validator\File\Size as SizeValidator,
    Zend\Validator\File\IsImage as ImageValidator,
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
        if (!($company = $this->_getCompany()))
            return new ViewModel();

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
        if (!($company = $this->_getCompany()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $commonEvent = new CommonEvent(
                    $this->getAuthentication()->getPersonObject(),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    (DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']) === false)
                        ? null : DateTime::createFromFormat('d#m#Y H#i', $formData['end_date'])
                );

                $this->getEntityManager()->persist($commonEvent);

                $event = new Event(
                    $commonEvent,
                    $company
                );

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if (
                        '' != $formData['location_' . $language->getAbbrev()] && '' != $formData['title_' . $language->getAbbrev()]
                            && '' != $formData['content_' . $language->getAbbrev()]
                    ) {
                        $translation = new Translation(
                            $commonEvent,
                            $language,
                            $formData['location_' . $language->getAbbrev()],
                            $formData['title_' . $language->getAbbrev()],
                            $formData['content_' . $language->getAbbrev()]
                        );
                        $commonEvent->addTranslation($translation);
                        $this->getEntityManager()->persist($translation);
                    }
                }

                $event->getEvent()->updateName();
                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully created!'
                    )
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
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $event->getEvent());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $event->getEvent()
                    ->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']) == false ? null : DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']));

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = $event->getEvent()->getTranslation($language, false);

                    if ($translation) {
                        $translation->setLocation($formData['location_' . $language->getAbbrev()])
                            ->setTitle($formData['title_' . $language->getAbbrev()])
                            ->setContent($formData['content_' . $language->getAbbrev()]);
                    } else {
                        if (
                            '' != $formData['location_' . $language->getAbbrev()] && '' != $formData['title_' . $language->getAbbrev()]
                                && '' != $formData['content_' . $language->getAbbrev()]
                        ) {
                            echo 'succes\n';
                            $translation = new Translation(
                                    $event->getEvent(),
                                    $language,
                                    $formData['location_' . $language->getAbbrev()],
                                    $formData['title_' . $language->getAbbrev()],
                                    $formData['content_' . $language->getAbbrev()]
                                );
                            $event->getEvent()->addTranslation($translation);
                            $this->getEntityManager()->persist($translation);
                        }
                    }
                }
                $event->getEvent()->updateName();
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully edited!'
                    )
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

        if (!($event = $this->_getEvent()))
            return new ViewModel();

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
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $form = new PosterForm();
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

    public function uploadAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        if ($this->getRequest()->isPost()) {
            $filePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('calendar.poster_path');

            $upload = new FileTransfer();
            $upload->addValidator(new SizeValidator(array('max' => '10MB')));
            $upload->addValidator(new ImageValidator());

            if ($upload->isValid()) {
                $upload->receive();

                $image = new Imagick($upload->getFileName());

                if ($event->getEvent()->getPoster() != '' || $event->getEvent()->getPoster() !== null) {
                    $fileName = '/' . $event->getEvent()->getPoster();
                } else {
                    $fileName = '';
                    do{
                        $fileName = '/' . sha1(uniqid());
                    } while (file_exists($filePath . $fileName));
                }
                $image->writeImage($filePath . $fileName);
                $event->getEvent()->setPoster($fileName);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The event\'s poster has successfully been updated!'
                    )
                );

                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info' => array(
                            'info' => array(
                                'name' => $fileName,
                            )
                        )
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

    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the company!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No company with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $company;
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
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Event')
            ->findOneById($this->getParam('id'));

        if (null === $event) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No event with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $event;
    }
}
