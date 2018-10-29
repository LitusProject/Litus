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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin;

use BrBundle\Component\Document\Generator\Pdf\Overview as PdfGenerator;
use BrBundle\Entity\Collaborator;
use BrBundle\Entity\Company;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Zend\Http\Headers;
use Zend\View\Model\ViewModel;

/**
 * OverviewController.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class OverviewController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function personAction()
    {
        list($carray, $marray, $totals) = $this->getPersonOverview();

        return new ViewModel(
            array(
                'carray' => $carray,
                'marray' => $marray,
                'totals' => $totals,
                'em'     => $this->getEntityManager(),
            )
        );
    }

    public function companyAction()
    {
        list($array, $totals) = $this->getCompanyOverview();

        return new ViewModel(
            array(
                'array'  => $array,
                'totals' => $totals,
                'em'     => $this->getEntityManager(),
            )
        );
    }

    public function personviewAction()
    {
        if (!($person = $this->getCollaboratorEntity())) {
            return new ViewModel();
        }

        $currentYear = $this->getCurrentAcademicYear();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedByPersonByYearQuery($person, $currentYear),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'author'            => $person,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'em'                => $this->getEntityManager(),
            )
        );
    }

    public function csvAction()
    {
        $file = new CsvFile();
        $heading = array('Company Name', 'Contract Number', 'Author', 'Product', 'Amount', 'Price', 'Contract Total', 'Invoice');

        $ids1 = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractCompany();

        $ids2 = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice\ManualInvoice')
            ->findInvoiceCompanies();

        $ids = array_unique(array_merge($ids1, $ids2), SORT_REGULAR);

        $results = array();
        foreach ($ids as $id) {
            $company = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company')
                ->findOneById($id);

            $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\entity\Contract')
                ->findAllNewOrSignedByCompany($company);

            foreach ($contracts as $contract) {
                $contract->getOrder()->setEntityManager($this->getEntityManager());
                $totalContractValue = $contract->getOrder()->getTotalCostExclusive();

                $orderEntries = $contract->getOrder()->getEntries();

                $invoice = $this->getEntityManager()
                    ->getRepository('BrBundle\entity\Invoice\ContractInvoice')
                    ->findAllByOrder($contract->getOrder());

                foreach ($orderEntries as $entry) {
                    $results[] = array(
                        $company->getName(),
                        $contract->getFullContractNumber($this->getEntityManager()),
                        $contract->getAuthor()->getPerson()->getFullName(),
                        $entry->getProduct()->getName(),
                        $entry->getQuantity(),
                        $entry->getProduct()->getSignedPrice() / 100,
                        $totalContractValue,
                        $contract->isSigned() ? $invoice->getInvoiceNumber() : '/',
                    );
                }
            }

            $invoices = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Invoice\ManualInvoice')
                ->findAllByCompany($company);

            foreach ($invoices as $invoice) {
                $results[] = array(
                    $company->getName(),
                    'Manual',
                    $invoice->getAuthor()->getPerson()->getFullName(),
                    $invoice->getTitle(),
                    1,
                    $invoice->getPrice() / 100,
                    $invoice->getPrice() / 100,
                    $invoice->getInvoiceNumber(),
                );
            }
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="contracts_overview.csv"',
            'Content-Type'        => 'text/csv',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function pdfAction()
    {
        $file = new TmpFile();
        $document = new PdfGenerator($this->getEntityManager(), $file);
        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="contracts_overview.pdf"',
            'Content-Type'        => 'application/pdf',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
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
                'company'           => $company,
                'cpaginator'        => $paginator,
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

        $ids1 = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractCompany();

        $ids2 = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice\ManualInvoice')
            ->findInvoiceCompanies();

        $ids = array_unique(array_merge($ids1, $ids2), SORT_REGULAR);

        $collection = array();
        foreach ($ids as $id) {
            $company = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company')
                ->findOneById($id);

            $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedByCompany($company);

            ++$companyNmbr;

            $contracted = 0;
            $invoiced = 0;
            $paid = 0;
            $invoiceN = 0;

            foreach ($contracts as $contract) {
                $contract->getOrder()->setEntityManager($this->getEntityManager());
                $value = $contract->getOrder()->getTotalCostExclusive();
                $contracted += $value;
                $totalContracted += $value;

                if ($contract->isSigned()) {
                    $invoiced += $value;
                    $totalSigned += $value;

                    if ($contract->getOrder()->getInvoice()->isPaid()) {
                        $paid += $value;
                        $totalPaid += $value;
                    }
                }
            }

            $invoices = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Invoice\ManualInvoice')
                ->findAllByCompany($company);

            foreach ($invoices as $invoice) {
                $value = $invoice->getExclusivePrice();
                $totalSigned += $value;
                $invoiced += $value;
                $invoiceN += 1;

                if ($invoice->isPaid()) {
                    $paid += $value;
                    $totalPaid += $value;
                }
            }

            $collection[] = array(
                'company'  => $company,
                'amount'   => sizeof($contracts),
                'invoiceN' => $invoiceN,
                'contract' => $contracted,
                'invoiced' => $invoiced,
                'paid'     => $paid,
            );
        }
        $totals = array('amount' => $companyNmbr, 'contract' => $totalContracted, 'paid' => $totalPaid, 'signed' => $totalSigned);

        return array($collection, $totals);
    }

    /**
     * @return array
     */
    private function getPersonOverview()
    {
        $contractNmbr = 0;
        $manualNmbr = 0;
        $totalContracted = 0;
        $invoiceNmbr = 0;
        $totalInvoiced = 0;
        $totalMInvoiced = 0;
        $totalPaid = 0;

        $currentYear = $this->getCurrentAcademicYear();

        $ids = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractAuthorsByYear($currentYear);

        echo '<script>console.log(' . json_encode($ids) . ')</script>';

        $ccollection = array();
        foreach ($ids as $id) {
            $person = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Collaborator')
                ->findOneById($id);

            $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedByPersonByYear($person, $currentYear);

            if (count($contracts) == 0) {
                continue;
            }

            $contracted = 0;
            $invoiceN = 0;
            $invoiced = 0;
            $paid = 0;

            foreach ($contracts as $contract) {
                ++$contractNmbr;
                $contract->getOrder()->setEntityManager($this->getEntityManager());
                $value = $contract->getOrder()->getTotalCostExclusive();
                $contracted += $value;
                $totalContracted += $value;

                if ($contract->isSigned()) {
                    $invoiced += $value;
                    $totalInvoiced += $value;
                    $invoiceNmbr += 1;
                    $invoiceN += 1;

                    if ($contract->getOrder()->getInvoice()->isPaid()) {
                        $paid += $value;
                        $totalPaid += $value;
                    }
                }
            }

            $ccollection[] = array(
                'person'     => $person,
                'camount'    => sizeof($contracts),
                'iamount'    => $invoiceN,
                'invoiced'   => $invoiced,
                'contracted' => $contracted,
                'paid'       => $paid,
            );
        }

        $ids = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Invoice\ManualInvoice')
            ->findInvoiceAuthors();

        $mcollection = array();

        foreach ($ids as $id) {
            $person = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Collaborator')
                ->findOneById($id);

            $invoices = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Invoice\ManualInvoice')
                ->findAllByAuthor($person);

            $invoiceN = 0;
            $invoiced = 0;
            $paid = 0;

            foreach ($invoices as $invoice) {
                $value = $invoice->getExclusivePrice();
                $totalMInvoiced += $value;
                $invoiced += $value;
                $manualNmbr += 1;
                $invoiceN += 1;

                if ($invoice->isPaid()) {
                    $paid += $value;
                    $totalPaid += $value;
                }
            }

            $mcollection[] = array(
                'person'   => $person,
                'iamount'  => $invoiceN,
                'invoiced' => $invoiced,
                'paid'     => $paid,
            );
        }

        $totals = array(
            'camount'    => $contractNmbr,
            'contracted' => $totalContracted,
            'invoiced'   => $totalInvoiced,
            'minvoiced'  => $totalMInvoiced,
            'paid'       => $totalPaid,
            'iamount'    => $invoiceNmbr,
            'mamount'    => $manualNmbr,
        );

        return array($ccollection, $mcollection, $totals);
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
