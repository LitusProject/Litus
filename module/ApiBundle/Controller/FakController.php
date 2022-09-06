<?php

namespace ApiBundle\Controller;

use DateTime;
use FakBundle\Entity\Log;
use FakBundle\Entity\Scanner;
use Laminas\View\Model\ViewModel;
use CommonBundle\Component\Controller\ActionController as ActionController;

/**
 * FakController
 */
class FakController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function addCheckInAction()
    {
//        if (!$this->getRequest()->isPost()) {
//            return $this->error(405, 'This endpoint can only be accessed through POST');
//        }
        $userData = $this->getRequest()->getPost('userData');
        $isDouble = $this->getRequest()->getPost('isDouble');
        $userData = '04690942646880;3000050586';
        $isDouble = false;
        $seperatedString = explode(';', $userData);

        $actionController = new ActionController();
        $rNumber = $actionController->getRNumberAPI($seperatedString[0], $seperatedString[1], $this->getEntityManager());

        $checkIn = $this->getEntityManager()
            ->getRepository('FakBundle\Entity\Scanner')
            ->findOneBy(array(
                    'username' => $rNumber,
                )
            );

        // TODO: Check how long ago previous check in was!

        if ($checkIn === null) {
            $checkIn = new Scanner($rNumber);
        }

        if ($isDouble) {
            $amount = 2;
        } else {
            $amount = 1;
        }

        $checkIn = $checkIn->addCheckin($amount);
        $now = new DateTime('now', new \DateTimeZone('Europe/Brussels'));

        $log = new Log($rNumber, $now, $isDouble);
        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'person' => $rNumber,
                    'amount' => $checkIn->getAmount(),
                ),
            ),
        );
    }
}