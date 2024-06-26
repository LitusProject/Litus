<?php

namespace LogisticsBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Reservation\Van as VanReservation;

class VanReservationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\Van')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        if ($this->getAuthentication()->isAuthenticated()) {
            $driver = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneById($this->getAuthentication()->getPersonObject()->getId());
            $isDriverLoggedIn = ($driver !== null);
        } else {
            $isDriverLoggedIn = false;
        }

        return new ViewModel(
            array(
                'isDriverLoggedIn'  => $isDriverLoggedIn,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\Van')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        if ($this->getAuthentication()->isAuthenticated()) {
            $driver = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneById($this->getAuthentication()->getPersonObject()->getId());
            $isDriverLoggedIn = ($driver !== null);
        } else {
            $isDriverLoggedIn = false;
        }

        return new ViewModel(
            array(
                'isDriverLoggedIn'  => $isDriverLoggedIn,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('logistics_van-reservation_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The reservation was succesfully created!'
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
        $reservation = $this->getVanReservationEntity();
        if ($reservation === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_van-reservation_edit', array('reservation' => $reservation));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The reservation was successfully updated!'
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

        $reservation = $this->getVanReservationEntity();
        if ($reservation === null) {
            return new ViewModel();
        }

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

        $reservation = $this->getVanReservationEntity();
        if ($reservation === null) {
            return new ViewModel();
        }

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
                    'driver' => $person->getFullName(),
                ),
            )
        );
    }

    public function unassignmeAction()
    {
        $this->initAjax();

        $reservation = $this->getVanReservationEntity();
        if ($reservation === null) {
            return new ViewModel();
        }

        $reservation->setDriver(null);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'driver' => '',
                ),
            )
        );
    }

    /**
     * @return VanReservation|null
     */
    private function getVanReservationEntity()
    {
        $reservation = $this->getEntityById('LogisticsBundle\Entity\Reservation\Van');

        if (!($reservation instanceof VanReservation)) {
            $this->flashMessenger()->error(
                'Error',
                'No reservation was found!'
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
