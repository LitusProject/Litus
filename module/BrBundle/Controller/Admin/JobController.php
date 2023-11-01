<?php

namespace BrBundle\Controller\Admin;

use BrBundle\Component\Document\Generator\Pdf\Invoice as InvoiceGenerator;
use BrBundle\Entity\Invoice;
use BrBundle\Entity\Invoice\History;
use BrBundle\Entity\Invoice\Manual as ManualInvoice;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File as FileUtil;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateTime;
use FormBundle\Component\Document\Generator\Zip as ZipGenerator;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use RuntimeException;

/**
 * JobController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class JobController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = null;
        if ($this->getParam('field') !== null) {
            $jobs = $this->search();
            if ($jobs === null) {
                return new ViewModel();
            }

            $paginator = $this->paginator()->createFromQuery(
                $jobs,
                $this->getParam('page')
            );
        }

        if ($paginator === null) {
            $paginator = $this->paginator()->createFromQuery(
                $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company\Job')
                    ->findAllActiveByTypeQuery(null, null, null, null),
                $this->getParam('page'));
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $jobs = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($jobs as $job) {
            $item = (object) array();
            $item->companyName = $job->getCompany()->getName();
            $item->name = $job->getName();
            $item->id = $job->getId();
            $item->summary = $job->getSummaryStriped();
            $item->type = $job->getTypeName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company\Job')
                    ->findAllActiveByNameQuery($this->getParam('string'));
            case 'company':
                return $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company\Job')
                    ->findAllActiveByCompanyQuery($this->getParam('string'));
        }
    }
}
