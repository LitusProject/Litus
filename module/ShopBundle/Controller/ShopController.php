<?php

namespace ShopBundle\Controller;

use DateInterval;
use DateTime;
use Laminas\View\Model\ViewModel;
use ShopBundle\Component\CanReserve\CanReserveResponse;
use ShopBundle\Entity\Reservation;
use ShopBundle\Entity\Session as SalesSession;

/**
 * ShopController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ShopController extends \CommonBundle\Component\Controller\ActionController\SiteController
{

    // TODO: Rename to reserveProductsAction()
    public function reserveproductsAction()
    {
        // reserve function should not be executed when user does not have permission to reserve
        $canReserveResponse = $this->canReserve();
        if (!$canReserveResponse->canReserve()) {
            return new ViewModel(
                array(
                    'shopName'           => $this->getShopName(),
                    'canReserveResponse' => $canReserveResponse,
                )
            );
        }

        if (!$this->saleSessionIsOpen()) {
            $this->flashMessenger()->error(
                'Error',
                'No session was found!'
            );
            $this->redirect()->toRoute('shop');
        }

        $salesSession = $this->getSalesSessionEntity();
        $stockEntries = $this->getStockEntries($salesSession);

        $reserveForm = $this->getForm(
            'shop_shop_reserve',
            array(
                'stockEntries' => $stockEntries,
                'salesSession' => $salesSession,
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $reserveForm->setData($formData);
            if ($reserveForm->isValid()) {
                $reservation = $reserveForm->hydrateObject();
                $reservation->setSalesSession($this->getSalesSessionEntity());
                if ($reservation->getSalesSession()->getFinalReservationDate() <= new DateTime()) {
                    $this->flashMessenger()->error(
                        'Error',
                        $this->getTranslator()->translate('You can only make reservations for sales sessions until the final reservation date.')
                    );
                } else {
                    $reservation->setPerson($this->getPersonEntity());
                    foreach ($stockEntries as $stockEntry) {
                        $product = $stockEntry->getProduct();
                        $amount = $formData['product-' . $product->getId()];
                        if ($amount) {
                            $tmpReservation = clone $reservation;
                            $tmpReservation->setProduct($product);
                            $tmpReservation->setAmount($amount);
                            $this->getEntityManager()->persist($tmpReservation);
                        }
                    }
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        $this->getTranslator()->translate('The reservation was successfully made!')
                    );
                }
                $this->redirect()->toRoute(
                    'shop',
                    array(
                        'action' => 'reservations',
                    )
                );

                return new ViewModel();
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    $this->getTranslator()->translate('An error occurred while processing your reservation!')
                );
            }
        }

        return new ViewModel(
            array(
                'canReserveResponse' => $canReserveResponse,
                'form'               => $reserveForm,
                'shopName'           => $this->getShopName(),
                'stockEntries'       => $stockEntries,
            )
        );
    }

    public function reserveAction()
    {
        // reserve function should not be executed when user does not have permission to reserve
        $canReserveResponse = $this->canReserve();
        if (!$canReserveResponse->canReserve()) {
            return new ViewModel(
                array(
                    'shopName'           => $this->getShopName(),
                    'canReserveResponse' => $canReserveResponse,
                )
            );
        }

        $salesSessions = $this->getSalesSessions();
        $sessionsForm = $this->getForm(
            'shop_shop_sessions',
            array(
                'salesSessions' => $salesSessions,
            )
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $sessionsForm->setData($formData);
            if ($sessionsForm->isValid()) {
                $this->redirect()->toRoute(
                    'shop',
                    array(
                        'action' => 'reserveproducts',
                        'id'     => $formData['salesSession'],
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'salesSessionsAvailable' => count($salesSessions) > 0,
                'canReserveResponse'     => $canReserveResponse,
                'form'                   => $sessionsForm,
                'shopName'               => $this->getShopName(),
            )
        );
    }

    public function reservationsAction()
    {
        // reserve function should not be executed when user does not have permission to reserve
        $canReserveResponse = $this->canReserve();
        if (!$canReserveResponse->canReserve()) {
            return new ViewModel(
                array(
                    'shopName'           => $this->getShopName(),
                    'canReserveResponse' => $canReserveResponse,
                )
            );
        }

        $reservations = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation')
            ->getAllCurrentReservationsByPerson($this->getPersonEntity());

        return new ViewModel(
            array(
                'canReserveResponse' => $canReserveResponse,
                'reservations'       => $reservations,
                'shopName'           => $this->getShopName(),
            )
        );
    }

    public function deleteReservationAction()
    {
        $reservation = $this->getReservationEntity();
        if ($reservation !== null) {
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

    public function consumeAction()
    {
        $form = $this->getForm('shop_shop_consume');
        $salesSession = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Session')
            ->findOneById($this->getParam('id'));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $username = $form->getData()['username'];
                if (str_contains($username, ';') && (strlen($username) == 25)) {
                    $seperatedString = explode(';', $username);
                    $rNumber = $this->getRNumberAPI($seperatedString[0], $seperatedString[1], $this->getEntityManager());
                    $reservations = $this->getEntityManager()
                        ->getRepository('ShopBundle\Entity\Reservation')
                        ->getAllReservationsByUsernameAndSalesSessionQuery($rNumber, $salesSession)->getResult();
                } else {
                    $reservations = $this->getEntityManager()
                        ->getRepository('ShopBundle\Entity\Reservation')
                        ->getAllReservationsByUsernameAndSalesSessionQuery($username, $salesSession)->getResult();
                }

                if (count($reservations) === 0) {
                    $this->flashMessenger()->error(
                        'Error',
                        $this->getTranslator()->translate('No reservations were found for the provided username.')
                    );
                    return new ViewModel(
                        array(
                            'form' => $form,
                            'session' => $salesSession,
                        )
                    );
                } else {
                    $consumed = $reservations[0]->getConsumed();
                    foreach ($reservations as $reservation) {
                        $reservation->setConsumed(true);
                    }
                    $this->getEntityManager()->flush(); // Sends cache to database

                    return new ViewModel(
                        array(
                            'reservations' => $reservations,
                            'consumed' => $consumed,
                            'form' => $form,
                            'session' => $salesSession,
                        )
                    );
                }
            }
        }
        return new ViewModel(
            array(
                'session' => $salesSession,
                'form' => $form,
            )
        );
    }

    public function rewardAction()
    {
        $salesSession = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Session')
            ->findOneById($this->getParam('id'));

        if (!$salesSession->getReward()) {
            $numberRewards = $salesSession->getAmountRewards() ? $salesSession->getAmountRewards() : 3;
            $allReservations = $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Reservation')
                ->findBySalesSessionQuery($salesSession)
                ->getResult();
            if (count($allReservations) > $numberRewards) {
                for ($i = 0; $i < $numberRewards; $i++) {
                    $rNumber = $allReservations[array_rand($allReservations)]->getPerson()->getUsername();
                    $reservations = $this->getEntityManager()
                        ->getRepository('ShopBundle\Entity\Reservation')
                        ->getAllReservationsByUsernameAndSalesSessionQuery($rNumber, $salesSession)->getResult();
                    while ($reservations[0]->getReward() && $reservations[0]->getConsumed()) {
                        $rNumber = $allReservations[array_rand($allReservations)]->getPerson()->getUsername();
                        $reservations = $this->getEntityManager()
                            ->getRepository('ShopBundle\Entity\Reservation')
                            ->getAllReservationsByUsernameAndSalesSessionQuery($rNumber, $salesSession)->getResult();
                    }

                    foreach ($reservations as $reservation) {
                        $reservation->setReward(true);
                    }
                }
            } else {
                $allReservations = $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Reservation')
                    ->findBySalesSessionQuery($salesSession)
                    ->getResult();

                foreach ($allReservations as $reservation) {
                    $reservation->setReward(true);
                }
            }
            $salesSession->setReward(true);
            $this->flashMessenger()->success('Succes', $this->getTranslator()->translate('The rewards are now randomized'));
        } else {
            $this->flashMessenger()->error('Error', $this->getTranslator()->translate('The rewards are already randomized'));
        }
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'shop',
            array(
                'action' => 'consume',
                'id'     => $salesSession->getId(),
            )
        );
        return new ViewModel();
    }

    public function getActiveBanEnd()
    {
        $activeBans = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation\Ban')
            ->findActiveByPersonQuery($this->getPersonEntity())
            ->getResult();

        if (count($activeBans) == 0) {
            return null;
        }

        return end($activeBans)->getEndTimestamp();
    }

    /**
     * @return CanReserveResponse
     */
    private function canReserve()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->redirect()->toRoute(
                'common_auth',
                array(
                    'redirect' => urlencode($this->getRequest()->getRequestUri()),
                )
            );
            return new CanReserveResponse(false);
        }

        // check if user has active reservation bans
        $activeBans = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Reservation\Ban')
            ->findActiveByPersonQuery($this->getPersonEntity())
            ->getResult();

        if (count($activeBans) > 0) {
            // retrieve the largest end timestamp of the currently active bans for this user
            $endTimestamps = array();
            $infinite = false;
            foreach ($activeBans as $ban) {
                if ($ban->getEndTimestamp() == null) {
                    $infinite = true;
                } else {
                    $endTimestamps[] = $ban->getEndTimestamp();
                }
            }
            $largestEndTimestamp = max($endTimestamps);

            // return response with error message stating the end of the user's ban period
            if ($infinite) {
                return new CanReserveResponse(
                    false,
                    'Your reservation privileges have been revoked for an indefinite amount of time.'
                );
            }
            return new CanReserveResponse(
                false,
                $this->getTranslator()->translate('You recently placed an order for a sandwich and/or salad at Theokot and did not pick it up within the specified hours. You will regain your reservation privileges at ') .
                '<b>' . $largestEndTimestamp->format('d/m/Y H:i') . '</b>.<br/><br/>'.
                $this->getTranslator()->translate('Please refer to the email you received for more information.')
            );
        }

        $configRepository = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config');

        if ($configRepository->getConfigValue('shop.reservation_default_permission')) {
            return new CanReserveResponse(true);
        }

        if ($configRepository->getConfigValue('shop.reservation_organisation_status_permission_enabled')) {
            $status = $this->getPersonEntity()->getOrganizationStatus($this->getCurrentAcademicYear());
            if ($status->getStatus() == $configRepository->getConfigValue('shop.reservation_organisation_status_permission_status')) {
                return new CanReserveResponse(true);
            }
        }

        if ($configRepository->getConfigValue('shop.reservation_shifts_general_enabled')) {
            if ($this->getTotalShiftCount() >= $configRepository->getConfigValue('shop.reservation_shifts_general_number')) {
                return new CanReserveResponse(true);
            }
        }

        if ($configRepository->getConfigValue('shop.reservation_shifts_permission_enabled')) {
            if ($this->getUnitShiftCount($configRepository->getConfigValue('shop.reservation_shifts_unit_id')) >= $configRepository->getConfigValue('shop.reservation_shifts_number')) {
                return new CanReserveResponse(true);
            }
        }

        return new CanReserveResponse(false);
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
     * @return \ShopBundle\Entity\Reservation|null
     */
    private function getReservationEntity()
    {
        $reservation = $this->getEntityById('ShopBundle\Entity\Reservation');

        if (!($reservation instanceof Reservation)) {
            $this->flashMessenger()->error(
                'Error',
                'No reservation was found!'
            );
            $this->redirect()->toRoute('shop');

            return null;
        }

        return $reservation;
    }

    /**
     * @return integer
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
     * @return integer
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

    /**
     * @return SalesSession|null
     */
    private function getSalesSessionEntity()
    {
        $salesSession = $this->getEntityById('ShopBundle\Entity\Session');

        if (!($salesSession instanceof SalesSession)) {
            $this->flashMessenger()->error(
                'Error',
                'No session was found!'
            );
            $this->redirect()->toRoute('shop');

            return null;
        }

        return $salesSession;
    }

    /**
     * @return array
     */
    private function getSalesSessions()
    {
        $interval = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shop.reservation_threshold')
        );

        $startDate = new DateTime();
        $endDate = clone $startDate;
        $endDate->add($interval);

        return $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Session')
            ->findAllReservationsPossibleInterval($startDate, $endDate);
    }

    /**
     * @return string
     */
    private function getShopName()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shop.name');
    }

    /**
     * @param  SalesSession $salesSession
     * @return array
     */
    private function getStockEntries($salesSession)
    {
        return $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Session\Stock')
            ->findBy(
                array(
                    'salesSession' => $salesSession,
                )
            );
    }

    private function saleSessionIsOpen()
    {
        $salesSessionId = $this->getSalesSessionEntity()->getId();
        $openSaleSessions = $this->getSalesSessions();
        $openSaleSessionsIds = array_map(
            function ($session) {
                return $session->getId();
            },
            $openSaleSessions
        );

        return in_array($salesSessionId, $openSaleSessionsIds);
    }
}
