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

use Zend\View\Model\ViewModel;

/**
 * OverviewController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class OverviewController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function personAction()
    {
        list($array, $totals) = $this->_getPersonOverview();

        return new ViewModel(
            array(
                'array' => $array,
                'totals' => $totals,
            )
        );
    }

    public function companyAction()
    {
        list($array, $totals) = $this->_getCompanyOverview();

        return new ViewModel(
            array(
                'array' => $array,
                'totals'=> $totals,
            )
        );
    }

    public function personviewAction()
    {
        if (!($person = $this->_getAuthor()))
            return new ViewModel();

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
        if (!($company = $this->_getCompany()))
            return new ViewModel();

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

    private function _getCompanyOverview()
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

            $companyNmbr = $companyNmbr + 1;

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

    private function _getPersonOverview()
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
                $contractNmbr = $contractNmbr + 1;
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
            'signed' => $totalSigned
        );

        return [$collection, $totals];

    }

    private function _getAuthor()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the author!'
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $person = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Collaborator')
            ->findOneById($this->getParam('id'));

        if (null === $person) {
            $this->flashMessenger()->error(
                'Error',
                'No person with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $person;
    }

    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the company!'
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));

        if (null === $company) {
            $this->flashMessenger()->error(
                'Error',
                'No company with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $company;
    }
}
