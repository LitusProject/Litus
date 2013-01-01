<?php

namespace CalendarBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile,
    CalendarBundle\Entity\Nodes\Event,
    CalendarBundle\Entity\Nodes\Translation,
    CalendarBundle\Form\Admin\Event\Add as AddForm,
    CalendarBundle\Form\Admin\Event\Edit as EditForm,
    CalendarBundle\Form\Admin\Event\Poster as PosterForm,
    DateTime,
    Imagick,
    ShiftBundle\Component\Document\Generator\EventPdf as EventPdfGenerator,
    Zend\Http\Headers,
    Zend\File\Transfer\Transfer as FileTransfer,
    Zend\Validator\File\Size as SizeValidator,
    Zend\Validator\File\IsImage as ImageValidator,
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
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CalendarBundle\Entity\Nodes\Event')
                ->findAllActive(0),
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
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CalendarBundle\Entity\Nodes\Event')
                ->findAllOld(),
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
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $event = new Event(
                    $this->getAuthentication()->getPersonObject(),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']) === false
                        ? null : DateTime::createFromFormat('d#m#Y H#i', $formData['end_date'])
                );
                $this->getEntityManager()->persist($event);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if (
                        '' != $formData['location_' . $language->getAbbrev()] && '' != $formData['title_' . $language->getAbbrev()]
                            && '' != $formData['content_' . $language->getAbbrev()]
                    ) {
                        $event->addTranslation(
                            new Translation(
                                $event,
                                $language,
                                $formData['location_' . $language->getAbbrev()],
                                $formData['title_' . $language->getAbbrev()],
                                $formData['content_' . $language->getAbbrev()]
                            )
                        );
                    }
                }
                $event->updateName();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_calendar',
                    array(
                        'action' => 'manage'
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
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $event);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $event->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']) == false ? null : DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']));

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = $event->getTranslation($language, false);

                    if (null !== $translation) {
                        $translation->setLocation($formData['location_' . $language->getAbbrev()])
                            ->setTitle($formData['title_' . $language->getAbbrev()])
                            ->setContent($formData['content_' . $language->getAbbrev()]);
                    } else {
                        if (
                            '' != $formData['location_' . $language->getAbbrev()] && '' != $formData['title_' . $language->getAbbrev()]
                                && '' != $formData['content_' . $language->getAbbrev()]
                        ) {
                            $translation = new Translation(
                                    $event,
                                    $language,
                                    $formData['location_' . $language->getAbbrev()],
                                    $formData['title_' . $language->getAbbrev()],
                                    $formData['content_' . $language->getAbbrev()]
                                );
                            $event->addTranslation($translation);
                            $this->getEntityManager()->persist($translation);
                        }
                    }
                }
                $event->updateName();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_calendar',
                    array(
                        'action' => 'manage'
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

        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function editPosterAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $form = new PosterForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('calendar.poster_path');

                $upload = new FileTransfer();
                $upload->addValidator(new SizeValidator(array('max' => '10MB')));
                $upload->addValidator(new ImageValidator());

                if ($upload->isValid()) {
                    $upload->receive();

                    $image = new Imagick($upload->getFileName());

                    if ($event->getPoster() != '' || $event->getPoster() !== null) {
                        $fileName = '/' . $event->getPoster();
                    } else {
                        $fileName = '';
                        do{
                            $fileName = '/' . sha1(uniqid());
                        } while (file_exists($filePath . $fileName));
                    }
                    $image->writeImage($filePath . $fileName);
                    $event->setPoster($fileName);

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'The event\'s poster has successfully been updated!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'admin_calendar',
                        array(
                            'action' => 'editPoster',
                            'id' => $event->getId(),
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'event' => $event,
                'form' => $form,
            )
        );
    }

    public function posterAction()
    {
        if (!($event = $this->_getEventByPoster()))
            return new ViewModel();

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

    public function pdfAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findBy(array('event' => $event), array('startDate' => 'ASC'));

        $file = new TmpFile();
        $document = new EventPdfGenerator($this->getEntityManager(), $event, $shifts, $file);
        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="shift_list.pdf"',
            'Content-Type'        => 'application/pdf',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
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
                'admin_calendar',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
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
                'admin_calendar',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $event;
    }

    private function _getEventByPoster()
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
                'admin_calendar',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
            ->findOneByPoster($this->getParam('id'));

        if (null === $event) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No event with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_calendar',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $event;
    }
}
