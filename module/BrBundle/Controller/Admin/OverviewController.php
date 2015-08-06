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

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Collaborator,
    BrBundle\Entity\Company,
    Zend\View\Model\ViewModel;

/**
 * OverviewController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class OverviewController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function personAction()
    {
        list($array, $totals) = $this->getPersonOverview();

        return new ViewModel(
            array(
                'array' => $array,
                'totals' => $totals,
                'em' => $this->getEntityManager(),
            )
        );
    }

    public function companyAction()
    {
        list($array, $totals) = $this->getCompanyOverview();

        return new ViewModel(
            array(
                'array' => $array,
                'totals' => $totals,
                'em' => $this->getEntityManager(),
            )
        );
    }

    public function personviewAction()
    {
        if (!($person = $this->getCollaboratorEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedByPersonQuery($person),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'author' => $person,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function companyviewAction()
    {
        if (!($company = $this->getCompanyEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedByCompanyQuery($company),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'company' => $company,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    /**
     * @return array
     */
    private function getCompanyOverview()
    {
        $companyNmbr = 0;
        $totalContracted = 0;
        $totalSigned = 0;
        $totalPaid = 0;

        $ids = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractCompany();

        $collection = array();
        foreach ($ids as $id) {
            $company = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company')
                ->findOneById($id);

            $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedByCompany($company);

            $companyNmbr++;

            $contracted = 0;
            $signed = 0;
            $paid = 0;

            foreach ($contracts as $contract) {
                $contract->getOrder()->setEntityManager($this->getEntityManager());
                $value = $contract->getOrder()->getTotalCost();
                $contracted = $contracted + $value;
                $totalContracted = $totalContracted + $value;

                if ($contract->isSigned()) {
                    $signed = $signed + $value;
                    $totalSigned = $totalSigned + $value;

                    if ($contract->getOrder()->getInvoice()->isPaid()) {
                        $paid = $paid + $value;
                        $totalPaid = $totalPaid + $value;
                    }
                }
            }

            $collection[] = array(
                'company' => $company,
                'amount' => sizeof($contracts),
                'contract' => $contracted,
                'signed' => $signed,
                'paid' => $paid,
            );
        }
        $totals = array('amount' => $companyNmbr, 'contract' => $totalContracted, 'paid' => $totalPaid, 'signed' => $totalSigned);

        return [$collection, $totals];
    }

    /**
     * @return array
     */
    private function getPersonOverview()
    {
        $contractNmbr = 0;
        $totalContracted = 0;
        $totalSigned = 0;
        $totalPaid = 0;

        $ids = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractAuthors();

        $collection = array();
        foreach ($ids as $id) {
            $person = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Collaborator')
                ->findOneById($id);

            $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedByPerson($person);

            $contracted = 0;
            $signed = 0;
            $paid = 0;

            foreach ($contracts as $contract) {
                $contractNmbr++;
                $contract->getOrder()->setEntityManager($this->getEntityManager());
                $value = $contract->getOrder()->getTotalCost();
                $contracted = $contracted + $value;
                $totalContracted = $totalContracted + $value;

                if ($contract->isSigned()) {
                    $signed = $signed + $value;
                    $totalSigned = $totalSigned + $value;

                    if ($contract->getOrder()->getInvoice()->isPaid()) {
                        $paid = $paid + $value;
                        $totalPaid = $totalPaid + $value;
                    }
                }
            }

            $collection[] = array(
                'person' => $person,
                'amount' => sizeof($contracts),
                'contract' => $contracted,
                'signed' => $signed,
                'paid' => $paid,
            );
        }

        $totals = array(
            'amount' => $contractNmbr,
            'contract' => $totalContracted,
            'paid' => $totalPaid,
            'signed' => $totalSigned,
        );

        return [$collection, $totals];
    }

    /**
     * @return Collaborator|null
     */
    private function getCollaboratorEntity()
    {
        $collaborator = $this->getEntityById('BrBundle\Entity\Collaborator');

        if (!($collaborator instanceof Collaborator)) {
            $this->flashMessenger()->error(
                'Error',
                'No collaborator was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $collaborator;
    }

    /**
     * @return Company|null
     */
    private function getCompanyEntity()
    {
        $company = $this->getEntityById('BrBundle\Entity\Company');

        if (!($company instanceof Company)) {
            $this->flashMessenger()->error(
                'Error',
                'No company was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $company;
    }
}
