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
        $overview = $this->_getPersonOverview();
        $array = $overview['array'];
        $totals = $overview['totals'];
        return new ViewModel(
            array(
                'array' => $array,
                'totals' => $totals,
            )
        );
    }

    public function companyAction()
    {
        $overview = $this->_getCompanyOverview();
        $array = $overview['array'];
        $totals = $overview['totals'];
        return new ViewModel(
            array(
                'array' => $array,
                'totals' => $totals,
            )
        );
    }

    public function personviewAction()
    {
        $person = $this->_getAuthor();

        $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findContractsByAuthorID($person);

        return new ViewModel(
            array(
                'contracts' => $contracts,
            )
        );
    }

    public function companyviewAction()
    {
        $company = $this->_getCompany();

        $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findContractsByCompanyID($company);

        return new ViewModel(
            array(
                'contracts' => $contracts,
            )
        );
    }

    private function _getCompanyOverview(){

        //TODO extremely dirty solution -> can be put in one single query normally!
        //TODO has to be cleaned up..

        $contractNmbr = 0;
        $totalContractRevenue = 0;
        $totalPaidRevenue = 0;

        $ids = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractCompany();
        $collection = array();
        foreach ($ids as $id => $val) {
            $result = array();

            $company = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findCompanyByID($val[1]);

            //TODO this query returns a array instead of a single element, has to be fixed.  So loop can be avoided.

            foreach ($company as $comp) {
                $result['company'] = $comp;
                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getContractAmountByCompany($comp);
                $result['amount'] = $amount;
                $contractNmbr += $amount;

                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getContractedRevenueByCompany($comp);
                $result['contract'] = $amount;
                $totalContractRevenue += $amount;

                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getPaidRevenueByCompany($comp);
                $result['paid'] = $amount;
                $totalPaidRevenue += $amount;

                array_push($collection, $result);
            }
        }
        $totals = array('amount' => $contractNmbr, 'contract' => $totalContractRevenue, 'paid' => $totalPaidRevenue);
        return array('array' => $collection,'totals' => $totals);
    }

    private function _getPersonOverview(){

        //TODO extremely dirty solution -> can be put in one single query normally!
        //TODO has to be cleaned up..

        $contractNmbr = 0;
        $totalContractRevenue = 0;
        $totalPaidRevenue = 0;

        $ids = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractAuthors();
        $collection = array();
        foreach ($ids as $id => $val) {
            $result = array();

            $person = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAuthorByID($val[1]);

            //TODO this query returns a array instead of a single element, has to be fixed.  So loop can be avoided.

            foreach ($person as $pers) {
                $result['person'] = $pers;
                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getContractAmountByPerson($pers);
                $result['amount'] = $amount;
                $contractNmbr += $amount;

                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getContractedRevenueByPerson($pers);
                $result['contract'] = $amount;
                $totalContractRevenue += $amount;

                $amount = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Contract')
                    ->getPaidRevenueByPerson($pers);
                $result['paid'] = $amount;
                $totalPaidRevenue += $amount;

                array_push($collection, $result);
            }
        }
        $totals = array('amount' => $contractNmbr, 'contract' => $totalContractRevenue, 'paid' => $totalPaidRevenue);
        return array('array' => $collection,'totals' => $totals);


    }

    private function _getAuthor()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the author!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $array = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAuthorByID($this->getParam('id'));
        foreach ($array as $pers) {
            $person = $pers;
        }

        if (null === $person) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No person with the given ID was found!'
                )
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
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the company!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_overview',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $array = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findCompanyByID($this->getParam('id'));
        foreach ($array as $comp) {
            $company = $comp;
        }

        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No company with the given ID was found!'
                )
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
