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
//        $paginator = $this->paginator()->createFromEntity(
//            'BrBundle\Entity\Company\Job',
//            $this->getParam('page'),
//            array(),
//            array(
//                'company.name' => 'ASC',
//                'type' => 'ASC',
//            )
//        );

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Job')
                ->findAllActiveByTypeQuery(null, null, null, null),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }
}
