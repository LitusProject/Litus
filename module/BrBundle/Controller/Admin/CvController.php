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

use BrBundle\Component\Document\Generator\Pdf\CvBook as CvBookGenerator;
use BrBundle\Entity\Cv\Entry;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * CvController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvController extends \BrBundle\Component\Controller\CvController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYear();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByAcademicYearQuery($academicYear),
            $this->getParam('page')
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears'       => $academicYears,
                'activeAcademicYear'  => $academicYear,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(),
            )
        );
    }

    public function exportAction()
    {
        $file = new TmpFile();
        $year = $this->getAcademicYear();

        $translator = $this->getTranslator();
        $locale = $this->getEntityManager()
            ->getRepository('CommonBUndle\Entity\General\Config')
            ->getConfigValue('br.cv_book_language');
        $translator->setLocale($locale);
        $document = new CvBookGenerator($this->getEntityManager(), $year, $file, $translator);

        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="cvbook-' . $year->getCode(true) . '.pdf"',
                'Content-type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function exportAcademicsAction()
    {
        $entries = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllByAcademicYear($this->getAcademicYear());

        $file = new CsvFile();
        $heading = array('First Name', 'Last Name', 'Email', 'Address', 'Phone', 'Study');

        $results = array();
        foreach ($entries as $entry) {
            $address = $entry->getAddress();
            $addressString = $address->getStreet() . ' ' . $address->getNumber();
            if ($address->getMailbox()) {
                $addressString .= ' (' . $address->getMailbox() . ')';
            }
            $addressString .= ', ' . $address->getPostal() . ' ' . $address->getCity() . ' ' . $address->getCountry();

            $results[] = array(
                $entry->getFirstName(),
                $entry->getLastName(),
                $entry->getEmail(),
                $addressString,
                $entry->getPhoneNumber(),
                $entry->getStudy()->getTitle(),
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="academics.csv"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $entry = $this->getEntryEntity();
        if ($entry === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Entry|null
     */
    private function getEntryEntity()
    {
        $entry = $this->getEntityById('BrBundle\Entity\Cv\Entry');

        if (!($entry instanceof Entry)) {
            $this->flashMessenger()->error(
                'Error',
                'No entry was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_cv_entry',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $entry;
    }
}
