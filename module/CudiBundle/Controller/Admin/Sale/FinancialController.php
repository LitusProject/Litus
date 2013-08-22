<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * FinancialController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FinancialController extends \CudiBundle\Component\Controller\ActionController
{
    public function overviewAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findAllByAcademicYear($academicYear);

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $data =  array(
            'totalTheoreticalRevenue' => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->getTheoreticalRevenueByAcademicYear($academicYear),
            'totalActualRevenue' => 0,
            'totalPurchasedAmount' => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->getPurchasedAmountByAcademicYear($academicYear),
            'totalNumberSold' => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findNumberByAcademicYear($academicYear),
            'uniqueClients' => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findUniqueClients($academicYear),
        );

        $organizationsList = array();
        foreach($organizations as $organization) {
            $organizationsList[$organization->getId()] = array(
                'entity' => $organization,
                'data' => array(
                    'totalTheoreticalRevenue' => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Session')
                        ->getTheoreticalRevenueByAcademicYear($academicYear, $organization),
                    'totalPurchasedAmount' => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Session')
                        ->getPurchasedAmountByAcademicYear($academicYear, $organization),
                ),
            );
        }

        foreach($sessions as $session) {
            $session->setEntityManager($this->getEntityManager());

            $data['totalActualRevenue'] += $session->getActualRevenue();
        }

        return new ViewModel(
            array(
                'organizations' => $organizations,
                'data' => $data,
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
            )
        );
    }
}
