<?php

namespace FakBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;

class ScannerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    // module/FakBundle/Controller/Admin/ScannerController.php

    public function manageAction()
    {
        $allCheckins = $this->getEntityManager()
            ->getRepository('FakBundle\Entity\Scanner')
            ->findBy(array(), array('amount' => 'DESC'));

        $filteredCheckins = array();
        $currentAcademicYear = $this->getCurrentAcademicYear();

        foreach ($allCheckins as $checkin) {
            $checkin->setEntityManager($this->getEntityManager());
            $person = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person')
                ->findOneByUsername($checkin->getUserName());

            // Only filter if person is an Academic
            if ($person instanceof \CommonBundle\Entity\User\Person\Academic) {
                $unit = $person->getUnit($currentAcademicYear);
                if ($unit && strtolower($unit->getName()) === 'fakbar') {
                    continue; // Skip this checkin
                }
            }
            $filteredCheckins[] = $checkin;
        }

        $paginator = $this->paginator()->createFromArray(
            $filteredCheckins,
            $this->getParam('page')
        );

        $totalAmount = 0;
        foreach ($filteredCheckins as $checkin) {
            $totalAmount += $checkin->getAmount();
        }

        $offset = ($paginator->getCurrentPageNumber() - 1) * $paginator->getItemCountPerPage();

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'totalAmount'       => $totalAmount,
                'offset'            => $offset,
            )
        );
    }

    public function logAction()
    {
        $logs = $this->getEntityManager()
            ->getRepository('FakBundle\Entity\Log')
            ->findAll();

        $paginator = $this->paginator()->createFromArray(
            $logs,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAllAction()
    {
        $allCheckins = $this->getEntityManager()
            ->getRepository('FakBundle\Entity\Scanner')
            ->findAll();

        foreach ($allCheckins as $checkin) {
            $this->getEntityManager()->remove($checkin);
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'All scans have been deleted.'
        );

        $this->redirect()->toRoute(
            'fak_admin_scanner',
            array(
                'action' => 'manage',
            )
        );
        return new ViewModel();
    }
}
