<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    ShiftBundle\Entity\Shift,
    ShiftBundle\Form\Admin\Shift\Add as AddForm,
    ShiftBundle\Form\Admin\Shift\Edit as EditForm,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * ShiftController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ShiftController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'ShiftBundle\Entity\Shift',
            $this->getParam('page'),
            array(),
            array(
                'startDate' => 'ASC',
            )
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
                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic');

                $manager = ('' == $formData['manager_id'])
                    ? $repository->findOneByUsername($formData['manager']) : $repository->findOneById($formData['manager_id']);

                $shift = new Shift(
                    $this->getAuthentication()->getPersonObject(),
                    $this->getCurrentAcademicYear(),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $manager,
                    $formData['nb_responsibles'],
                    $formData['nb_volunteers'],
                    $this->getEntityManager()
                        ->getRepository('ShiftBundle\Entity\Unit')
                        ->findOneById($formData['unit']),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Location')
                        ->findOneById($formData['location']),
                    $formData['name'],
                    $formData['description']
                );

                if ('' != $formData['event']) {
                    $shift->setEvent(
                        $this->getEntityManager()
                            ->getRepository('CalendarBundle\Entity\Nodes\Event')
                            ->findOneById($formData['event'])
                    );
                }

                $this->getEntityManager()->persist($shift);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The shift was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_shift',
                    array(
                        'action' => 'add'
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
        if (!($shift = $this->_getShift()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $shift);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                if ($shift->canEditDates()) {
                    $shift->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                        ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']));
                }

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic');

                $manager = ('' == $formData['manager_id'])
                    ? $repository->findOneByUsername($formData['manager']) : $repository->findOneById($formData['manager_id']);

                $shift->setManager($manager)
                    ->setNbResponsibles($formData['nb_responsibles'])
                    ->setNbVolunteers($formData['nb_volunteers'])
                    ->setUnit(
                        $this->getEntityManager()
                            ->getRepository('ShiftBundle\Entity\Unit')
                            ->findOneById($formData['unit'])
                    )
                    ->setLocation(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Location')
                            ->findOneById($formData['location'])
                    )
                    ->setName($formData['name'])
                    ->setDescription($formData['description']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The shift was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_shift',
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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($shift = $this->_getShift()))
            return new ViewModel();

        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.mail_name');

        $message = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.shift_deleted_mail');

        $subject = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.shift_deleted_mail_subject');

        $shiftString = $shift->getName() . ' from ' . $shift->getStartDate()->format('d/m/Y h:i') . ' to ' . $shift->getEndDate()->format('d/m/Y h:i');

        $mail = new Message();
        $mail->setBody(str_replace('{{ shift }}', $shiftString, $message))
            ->setFrom($mailAddress, $mailName)
            ->setSubject($subject);

        $mail->addTo($mailAddress, $mailName);

        foreach ($shift->getVolunteers() as $volunteer)
            $mail->addBcc($volunteer->getPerson()->getEmail(), $volunteer->getPerson()->getFullName());

        foreach ($shift->getResponsibles() as $responsible)
            $mail->addBcc($responsible->getPerson()->getEmail(), $responsible->getPerson()->getFullName());

        if ('development' != getenv('APPLICATION_ENV'))
            $this->getMailTransport()->send($mail);

        $this->getEntityManager()->remove(
            $shift->prepareRemove()
        );

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getShift()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the shift!'
                )
            );

            $this->redirect()->toRoute(
                'admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getParam('id'));

        if (null === $shift) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No shift with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_shift',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $shift;
    }
}
