<?php

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Component\Util\File\TmpFile;
use CudiBundle\Component\Document\Generator\Financial as FinancialGenerator;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * FinancialController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FinancialController extends \CudiBundle\Component\Controller\ActionController
{
    public function overviewAction()
    {
        $academicYear = $this->getAcademicYearEntity();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findAllByAcademicYear($academicYear);

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $data = array(
            'totalTheoreticalRevenue' => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->getTheoreticalRevenueByAcademicYear($academicYear),
            'totalActualRevenue'      => 0,
            'totalPurchasedAmount'    => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->getPurchasedAmountByAcademicYear($academicYear),
            'totalNumberSold'         => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findNumberByAcademicYear($academicYear),
            'uniqueClients'           => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                ->findUniqueClientsByAcademicYear($academicYear),
            'totalOrderedPrice'       => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->getOrderedAmountByAcademicYear($academicYear),
            'totalNumberOrdered'      => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->getNumberByAcademicYear($academicYear),
            'totalDeliveredPrice'     => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Delivery')
                ->getDeliveredAmountByAcademicYear($academicYear),
            'totalNumberDelivered'    => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Delivery')
                ->getNumberByAcademicYear($academicYear),
        );

        $organizationsList = array();
        foreach ($organizations as $organization) {
            $organizationsList[$organization->getId()] = array(
                'entity' => $organization,
                'data'   => array(
                    'totalTheoreticalRevenue' => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Session')
                        ->getTheoreticalRevenueByAcademicYear($academicYear, $organization),
                    'totalPurchasedAmount' => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Session')
                        ->getPurchasedAmountByAcademicYear($academicYear, $organization),
                ),
            );
        }

        foreach ($sessions as $session) {
            $session->setEntityManager($this->getEntityManager());

            $data['totalActualRevenue'] += $session->getActualRevenue();
        }

        return new ViewModel(
            array(
                'organizationsList'        => $organizationsList,
                'data'                     => $data,
                'academicYears'            => $academicYears,
                'activeAcademicYear'       => $academicYear,
                'otherOrganizationEnabled' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.enable_other_organization'),
            )
        );
    }

    public function periodAction()
    {
        $academicYear = $this->getAcademicYearEntity();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizationsList = array();
        $data = array();

        $form = $this->getForm('cudi_sale_financial_period');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $startDate = DateTime::createFromFormat('d/m/Y', $formData['start_date']);
                $endDate = DateTime::createFromFormat('d/m/Y', $formData['end_date']);

                $sessions = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Session')
                    ->findAllBetween($startDate, $endDate);

                $organizations = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findAll();

                $data = array(
                    'totalTheoreticalRevenue' => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Session')
                        ->getTheoreticalRevenueBetween($startDate, $endDate),
                    'totalActualRevenue'      => 0,
                    'totalPurchasedAmount'    => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Session')
                        ->getPurchasedAmountBetween($startDate, $endDate),
                    'totalNumberSold'         => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                        ->findNumberBetween($startDate, $endDate),
                    'uniqueClients'           => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\SaleItem')
                        ->findUniqueClientsBetween($startDate, $endDate),
                    'totalOrderedPrice'       => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                        ->getOrderedAmountBetween($startDate, $endDate),
                    'totalNumberOrdered'      => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                        ->getNumberBetween($startDate, $endDate),
                    'totalDeliveredPrice'     => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Delivery')
                        ->getDeliveredAmountBetween($startDate, $endDate),
                    'totalNumberDelivered'    => $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Stock\Delivery')
                        ->getNumberBetween($startDate, $endDate),
                );

                foreach ($organizations as $organization) {
                    $organizationsList[$organization->getId()] = array(
                        'entity' => $organization,
                        'data'   => array(
                            'totalTheoreticalRevenue' => $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Session')
                                ->getTheoreticalRevenueBetween($startDate, $endDate, $organization),
                            'totalPurchasedAmount' => $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Sale\Session')
                                ->getPurchasedAmountBetween($startDate, $endDate, $organization),
                        ),
                    );
                }

                foreach ($sessions as $session) {
                    $session->setEntityManager($this->getEntityManager());

                    $data['totalActualRevenue'] += $session->getActualRevenue();
                }
            }
        }

        return new ViewModel(
            array(
                'form'                     => $form,
                'academicYears'            => $academicYears,
                'activeAcademicYear'       => $academicYear,
                'organizationsList'        => $organizationsList,
                'data'                     => $data,
                'otherOrganizationEnabled' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('secretary.enable_other_organization'),
            )
        );
    }

    public function exportAction()
    {
        $file = new TmpFile();
        $document = new FinancialGenerator($this->getEntityManager(), $this->getAcademicYearEntity(), $file);
        $document->generate();

        $now = new DateTime();
        $filename = 'financial_' . $now->format('Y_m_d') . '.pdf';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }
}
