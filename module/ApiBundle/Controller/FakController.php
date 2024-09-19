<?php

namespace ApiBundle\Controller;

use CommonBundle\Component\Controller\ActionController;
use DateTime;
use FakBundle\Entity\Log;
use FakBundle\Entity\Scanner;
use Laminas\View\Model\ViewModel;

/**
 * FakController
 */
class FakController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function addCheckInAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }
        $userData = $this->getRequest()->getPost('userData');
        $seperatedString = explode(';', $userData);
    
        if ($seperatedString[1] === '') {
            return new ViewModel(
                array(
                    'result' => (object) array(
                        'status' => 'error',
                        'reason' => 'toShort',
                    ),
                ),
            );
        }

        $actionController = new ActionController();
        $rNumber = $actionController->getRNumberAPI($seperatedString[0], $seperatedString[1], $this->getEntityManager());

        $now = new DateTime('now', new \DateTimeZone('Europe/Brussels'));

        if ('22' <= $now->format('H') && $now->format('H') < '23') {
            $isDouble = true;
        } else {
            $isDouble = false;
        }

        // Determine which date to use to check if check in is valid
        $cutOffString = str_replace(
            '{{ currentDate }}',
            $now->format('d-m-Y'),
            '{{ currentDate }} 12:00:00'
        );
        $cutOffTime = new DateTime($cutOffString, new \DateTimeZone('Europe/Brussels'));

        $period = new \DateInterval('P1D');

        if ($now->format('H-i-s') < $cutOffTime->format('H-i-s')) {
            $cutOffTime->sub($period);
        }

        $checkIn = $this->getEntityManager()
            ->getRepository('FakBundle\Entity\Scanner')
            ->findOneBy(
                array(
                    'username' => $rNumber,
                )
            );

        if ($checkIn !== null) {
            $lastCheckin = $checkIn->getLastCheckin();

            if ($lastCheckin->format('d-m-Y H-i-s') > $cutOffTime->format('d-m-Y H-i-s')) {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'error',
                            'person' => $rNumber,
                            'amount' => $checkIn->getAmount() ? : '0',
                        ),
                    ),
                );
            }
        }

        if ($checkIn === null) {
            $checkIn = new Scanner($rNumber);
            $this->getEntityManager()->persist($checkIn);
            $this->getEntityManager()->flush();
        }

        if ($isDouble) {
            $amount = 2;
        } else {
            $amount = 1;
        }

        $checkIn = $checkIn->addCheckin($amount);
        $checkIn = $checkIn->setLastChecin($now);

        $log = new Log($rNumber, $now, $isDouble);
        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'person' => $rNumber,
                    'amount' => $checkIn->getAmount(),
                    'double' => $isDouble,
                ),
            ),
        );
    }

    public function addCheckInUsernameAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $userName = $this->getRequest()->getPost('userName');

        $now = new DateTime('now', new \DateTimeZone('Europe/Brussels'));

        if ('22' <= $now->format('H') && $now->format('H') < '23') {
            $isDouble = true;
        } else {
            $isDouble = false;
        }

        $checkIn = $this->getEntityManager()
            ->getRepository('FakBundle\Entity\Scanner')
            ->findOneBy(
                array(
                    'username' => $userName,
                )
            );

        if ($checkIn !== null) {
            $lastCheckin = $checkIn->getLastCheckin();

            $time_diff = ($now->getTimeStamp() - $lastCheckin->getTimeStamp()) / 60;

            if ($time_diff < 60 * 17) {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'error',
                            'amount' => $checkIn->getAmount() ? : '0',
                        ),
                    ),
                );
            }
        }

        if ($checkIn === null) {
            $checkIn = new Scanner($userName);
            $this->getEntityManager()->persist($checkIn);
            $this->getEntityManager()->flush();
        }

        if ($isDouble) {
            $amount = 2;
        } else {
            $amount = 1;
        }

        $checkIn = $checkIn->addCheckin($amount);
        $checkIn = $checkIn->setLastChecin($now);

        $log = new Log($userName, $now, $isDouble);
        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'amount' => $checkIn->getAmount(),
                    'double' => $isDouble,
                ),
            ),
        );
    }
}
