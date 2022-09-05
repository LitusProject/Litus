<?php

namespace FakBundle\Controller\Admin;

use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

class ScannerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $allCheckins = $this->getEntityManager()
            ->getRepository('FakBundle\Entity\Scanner')
            ->findAll();

        $paginator = $this->paginator()->createFromArray(
            $allCheckins,
            $this->getParam('page')
        );

        $totalAmount = 0;

        foreach ($allCheckins as $checkin) {
            $totalAmount += $checkin->getAmount();
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'totalAmount' => $totalAmount,
            )
        );
    }

    
}