<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company\Event,
    CalendarBundle\Form\Admin\Event\Add as AddForm,
    CalendarBundle\Form\Admin\Event\Edit as EditForm,
    CalendarBundle\Form\Admin\Event\Poster as PosterForm,
    CalendarBundle\Entity\Nodes\Event as CommonEvent,
    CalendarBundle\Entity\Nodes\Translation,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    Imagick,
    Zend\Http\Headers,
    Zend\File\Transfer\Transfer as FileTransfer,
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

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Event')
                ->findAllByCompany($company),
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
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $commonEvent = new CommonEvent(
                    $this->getAuthentication()->getPersonObject(),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']) == false ? null : DateTime::createFromFormat('d#m#Y H#i', $formData['end_date'])
                );

                $event = new Event(
                    $commonEvent,
                    $company
                );

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = new Translation(
                        $commonEvent,
                        $language,
                        $formData['location_' . $language->getAbbrev()],
                        $formData['title_' . $language->getAbbrev()],
                        $formData['content_' . $language->getAbbrev()]
                    );
                    $this->getEntityManager()->persist($translation);
                }

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
                    'admin_company_event',
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
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $event->getEvent()
                    ->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']) == false ? null : DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']));

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = $event->getEvent()->getTranslation($language);

                    if ($translation) {
                        $translation->setLocation($formData['location_' . $language->getAbbrev()])
                            ->setTitle($formData['title_' . $language->getAbbrev()])
                            ->setContent($formData['content_' . $language->getAbbrev()]);
                    } else {
                        $translation = new Translation(
                            $event,
                            $language,
                            $formData['location_' . $language->getAbbrev()],
                            $formData['title_' . $language->getAbbrev()],
                            $formData['content_' . $language->getAbbrev()]
                        );
                        $this->getEntityManager()->persist($translation);
                    }
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_company_event',
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
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function editPosterAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $form = new PosterForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('calendar.poster_path');

                $file = new FileTransfer();
                $file->receive();

                $image = new Imagick($file->getFileName());

                if ($event->getEvent()->getPoster() != '' || $event->getEvent()->getPoster() !== null) {
                    $fileName = $event->getEvent()->getPoster();
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

                $this->redirect()->toRoute(
                    'admin_company_event',
                    array(
                        'action' => 'editPoster',
                        'id' => $event->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'event' => $event,
                'form' => $form,
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
                'admin_company',
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
                'admin_company',
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
                'admin_company',
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
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $event;
    }
}
