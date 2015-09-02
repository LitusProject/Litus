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

namespace ShopBundle\Controller;

use DateTime,
    Zend\View\Model\ViewModel;

/**
 * ShopController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ShopController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    /**
	 * @return string
	 */
    private function getShopName()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shop.name');
    }

    public function reserveAction()
    {
        $canReserve = $this->canReserve();
        if (!$canReserve) {
            $this->flashMessenger()->error(
                'Error',
                $this->getTranslator()->translate('You are not allowed to make reservations!')
            );

            return new ViewModel();
        }

        $reserveForm = $this->getForm('shop_shop_reserve');
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $reserveForm->setData($formData);

            if ($reserveForm->isValid()) {
                $reservation = $reserveForm->hydrateObject();
                if ($reservation->getSalesSession()->getStartDate() <= new DateTime()) {
                    $this->flashMessenger()->error(
                        'Error',
                        $this->getTranslator()->translate('You can only make reservations for sales sessions that have not started yet.')
                    );
                } else {
                    $reservation->setPerson($this->getPersonEntity());
                    $this->getEntityManager()->persist($reservation);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        $this->getTranslator()->translate('The reservation was successfully made!')
                    );
                }
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    $this->getTranslator()->translate('An error occurred while processing your reservation!')
                );
            }
            $this->redirect()->toRoute(
                'shop',
                array(
                    'action' => 'reservations',
                )
            );
        }

        return new ViewModel(
            array(
                'canReserve' => $canReserve,
                'form' => $reserveForm,
                'shopName' => $this->getShopName(),
            )
        );
    }

    public function reservationsAction()
    {
        $canReserve = $this->canReserve();

        $reservations = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation')
            ->getAllCurrentReservationsByPerson($this->getPersonEntity());

        return new ViewModel(
            array(
                'canReserve' => $canReserve,
                'reservations' => $reservations,
                'shopName' => $this->getShopName(),
            )
        );
    }

    public function deleteReservationAction()
    {
        if ($reservation = $this->getEntityById('ShopBundle\Entity\Reservation')) {
            $canBeDeleted = true;
            $canBeDeleted = $canBeDeleted && $reservation->getPerson()->getId() == $this->getPersonEntity()->getId();
            $canBeDeleted = $canBeDeleted && $reservation->getSalesSession()->getStartDate() > new DateTime();
            if (!$canBeDeleted) {
                $this->flashMessenger()->error('Error', $this->getTranslator()->translate('You don\'t have permission to cancel this reservation.'));
            } else {
                $this->getEntityManager()->remove($reservation);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success('Success', $this->getTranslator()->translate('Your reservation was successfully cancelled'));
            }
        } else {
            $this->flashMessenger()->error('Error', $this->getTranslator()->translate('An error occurred while trying to cancel your reservation'));
        }

        $this->redirect()->toRoute(
            'shop',
            array(
                'action' => 'reservations',
            )
        );

        return new ViewModel();
    }

    /**
	 * @return \CommonBundle\Entity\User\Person|null
	 */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
	 * @return bool
	 */
    private function canReserve()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return false;
        }

        //reservation permissions
        $reservationPermission = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\ReservationPermission')
            ->find($this->getPersonEntity());
        if ($reservationPermission) {
            return $reservationPermission->getReservationsAllowed();
        }

        $configRepository = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config');

        //default permission
        if ($configRepository->getConfigValue('shop.reservation_default_permission')) {
            return true;
        }

        //organization role
        if ($configRepository->getConfigValue('shop.reservation_organisation_status_permission_enabled')) {
            $status = $this->getPersonEntity()->getOrganizationStatus($this->getCurrentAcademicYear());
            if ($status->getStatus() == $configRepository->getConfigValue('shop.reservation_organisation_status_permission_status')) {
                return true;
            }
        }

        //total shifts
        if ($configRepository->getConfigValue('shop.reservation_shifts_general_enabled')) {
            if ($this->getTotalShiftCount() >= $configRepository->getConfigValue('shop.reservation_shifts_general_number')) {
                return true;
            }
        }

        //shifts for unit
        if ($configRepository->getConfigValue('shop.reservation_shifts_permission_enabled')) {
            if ($this->getUnitShiftCount($configRepository->getConfigValue('shop.reservation_shifts_unit_id')) >= $configRepository->getConfigValue('shop.reservation_shifts_number')) {
                return true;
            }
        }

        return false;
    }

    /**
	 * @return int
	 */
    private function getTotalShiftCount()
    {
        $shiftCount = 0;
        $now = new DateTime();
        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsVolunteer($this->getPersonEntity(), $this->getCurrentAcademicYear());
        foreach ($shifts as $shift) {
            if ($shift->getStartDate() > $now) {
                continue;
            }
            $shiftCount++;
        }

        return $shiftCount;
    }

    /**
	 * @param $unitId
	 * @return int
	 */
    private function getUnitShiftCount($unitId)
    {
        $shiftCount = 0;
        $now = new DateTime();
        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsVolunteer($this->getPersonEntity(), $this->getCurrentAcademicYear());
        foreach ($shifts as $shift) {
            if ($shift->getStartDate() > $now) {
                continue;
            }
            if ($shift->getUnit()->getId() != $unitId) {
                continue;
            }
            $shiftCount++;
        }

        return $shiftCount;
    }
}
