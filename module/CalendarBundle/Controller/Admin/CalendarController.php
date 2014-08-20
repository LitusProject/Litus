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
    CalendarBundle\Entity\Node\Translation,
    CalendarBundle\Form\Admin\Event\Add as AddForm,
    CalendarBundle\Form\Admin\Event\Edit as EditForm,
    CalendarBundle\Form\Admin\Event\Poster as PosterForm,
    DateTime,
    Imagick,
    Zend\Http\Headers,
    Zend\File\Transfer\Adapter\Http as FileTransfer,
    Zend\InputFilter\InputInterface,
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
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            $startDate = self::_loadDate($formData['start_date']);

            if ($form->isValid() && $startDate) {
                $formData = $form->getFormData($formData);

                $event = new Event($this->getAuthentication()->getPersonObject(), $startDate, self::_loadDate($formData['end_date']));
                $this->getEntityManager()->persist($event);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach ($languages as $language) {
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

                $this->flashMessenger()->success(
                    'Success',
                    'The event was successfully added!'
                );

                $this->redirect()->toRoute(
                    'calendar_admin_calendar',
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

            $startDate = self::_loadDate($formData['start_date']);

            if ($form->isValid() && $startDate) {
                $formData = $form->getFormData($formData);

                $event->setStartDate($startDate)
                    ->setEndDate(self::_loadDate($formData['end_date']));

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach ($languages as $language) {
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

                $this->flashMessenger()->success(
                    'Success',
                    'The event was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'calendar_admin_calendar',
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

    public function uploadAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $form = new PosterForm();

        if ($this->getRequest()->isPost()) {
            $filePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('calendar.poster_path');

            $upload = new FileTransfer();
            $inputFilter = $form->getInputFilter()->get('poster');
            if ($inputFilter instanceof InputInterface)
                $upload->setValidators($inputFilter->getValidatorChain()->getValidators());

            if ($upload->isValid()) {
                $upload->receive();

                $image = new Imagick($upload->getFileName('poster'));
                unlink($upload->getFileName('poster'));

                if ($event->getPoster() != '' || $event->getPoster() !== null) {
                    $fileName = '/' . $event->getPoster();
                } else {
                    do {
                        $fileName = '/' . sha1(uniqid());
                    } while (file_exists($filePath . $fileName));
                }

                $image->writeImage($filePath . $fileName);
                $event->setPoster($fileName);

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
                            )
                        )
                    )
                );
            } else {
                $formErrors = array();

                if (sizeof($upload->getMessages()) > 0)
                    $formErrors['poster'] = $upload->getMessages();

                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form' => array(
                            'errors' => $formErrors
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

    /**
     * @return Event|null
     */
    private function _getEvent()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the event!'
            );

            $this->redirect()->toRoute(
                'calendar_admin_calendar',
                array(
                    'action' => 'manage'
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
                    'action' => 'manage'
                )
            );

            return;
        }

        return $event;
    }

    /**
     * @return Event|null
     */
    private function _getEventByPoster()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the event!'
            );

            $this->redirect()->toRoute(
                'calendar_admin_calendar',
                array(
                    'action' => 'manage'
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
                    'action' => 'manage'
                )
            );

            return;
        }

        return $event;
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
