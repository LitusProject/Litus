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

namespace LogisticsBundle\Controller\Admin;

use LogisticsBundle\Form\Admin\VanReservation\Add as AddForm,
    DateTime,
    LogisticsBundle\Entity\Driver,
    LogisticsBundle\Form\Admin\VanReservation\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    LogisticsBundle\Entity\Reservation\ReservableResource,
    LogisticsBundle\Entity\Reservation\VanReservation,
    Zend\View\Model\ViewModel;

class VanReservationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
                ->findAllActive(),
            $this->getParam('page')
        );

        $current = $this->getAuthentication()->getPersonObject();
        if ($current != null) {
            $driver = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneById($current->getId());
            $isDriverLoggedIn = ($driver !== null);
        } else {
            $isDriverLoggedIn = false;
        }

        return new ViewModel(
            array(
                'currentUser' => $current,
                'isDriverLoggedIn' => $isDriverLoggedIn,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
                ->findAllOld(),
            $this->getParam('page')
        );

        $current = $this->getAuthentication()->getPersonObject();
        if ($current != null) {
            $driver = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneById($current->getId());
            $isDriverLoggedIn = ($driver !== null);
        } else {
            $isDriverLoggedIn = false;
        }

        return new ViewModel(
            array(
                'currentUser' => $current,
                'isDriverLoggedIn' => $isDriverLoggedIn,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');

                $passenger = ('' == $formData['passenger_id'])
                    ? $repository->findOneByUsername($formData['passenger']) : $repository->findOneById($formData['passenger_id']);

                $driver = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Driver')
                    ->findOneById($formData['driver']);

                $van = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                    ->findOneByName(VanReservation::VAN_RESOURCE_NAME);

                $reservation = new VanReservation(
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $formData['reason'],
                    $formData['load'],
                    $van,
                    $formData['additional_info'],
                    $this->getAuthentication()->getPersonObject()
                );

                $reservation->setDriver($driver);
                $reservation->setPassenger($passenger);

                $this->getEntityManager()->persist($reservation);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The reservation was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'logistics_admin_van_reservation',
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
        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $form = new EditForm(
            $this->getEntityManager(), $this->getCurrentAcademicYear(), $reservation
        );

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');

                $passenger = ('' == $formData['passenger_id'])
                    ? $repository->findOneByUsername($formData['passenger']) : $repository->findOneById($formData['passenger_id']);

                $driver = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Driver')
                    ->findOneById($formData['driver']);

                $reservation->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']))
                    ->setReason($formData['reason'])
                    ->setLoad($formData['load'])
                    ->setAdditionalInfo($formData['additional_info'])
                    ->setDriver($driver)
                    ->setPassenger($passenger);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The reservation was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'logistics_admin_van_reservation',
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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $this->getEntityManager()->remove($reservation);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function assignmeAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $person = $this->getAuthentication()->getPersonObject();
        $driver = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findOneById($person->getId());

        $reservation->setDriver($driver);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    "driver" => $person->getFullName(),
                ),
            )
        );
    }

    public function unassignmeAction()
    {
        $this->initAjax();

        if (!($reservation = $this->_getReservation()))
            return new ViewModel();

        $reservation->setDriver(null);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    "driver" => "",
                ),
            )
        );
    }

    private function _getReservation()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the reservation!'
                )
            );

            $this->redirect()->toRoute(
                'logistics_admin_van_reservation',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $reservation = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\VanReservation')
            ->findOneById($this->getParam('id'));

        if (null === $reservation) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'logistics_admin_van_reservation',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $reservation;
    }
}
