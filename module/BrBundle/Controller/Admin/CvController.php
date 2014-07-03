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

use BrBundle\Component\Document\Generator\Pdf\CvBook as CvBookGenerator,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    CommonBundle\Component\Document\Generator\Csv as CsvGenerator,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

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
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function exportAction()
    {
        $academicYear = $this->getAcademicYear();

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
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="cvbook-' . $year->getCode(true) . '.pdf"',
            'Content-type'        => 'application/pdf',
        ));
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
        $language = $this->getLanguage();
        $heading = array('First Name', 'Last Name', 'Email', 'Address', 'Phone', 'Study');

        $results = array();
        foreach ($entries as $entry) {
            $address = $entry->getAddress();
            $addressString = $address->getStreet() . ' ' . $address->getNumber();
            if ($address->getMailbox())
                $addressString = $addressString . ' (' . $address->getMailbox() . ')';
            $addressString = $addressString . ', ' . $address->getPostal() . ' ' . $address->getCity() . ' ' . $address->getCountry();

            $results[] = array(
                $entry->getFirstName(),
                $entry->getLastName(),
                $entry->getEmail(),
                $addressString,
                $entry->getPhoneNumber(),
                $entry->getStudy()->getFullTitle()
            );
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="academics.csv"',
            'Content-Type'        => 'text/csv',
        ));
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

        if (!($entry = $this->_getEntry()))
            return new ViewModel();

        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getEntry()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the entry!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_cv_entry',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $entry = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findOneById($this->getParam('id'));

        if (null === $entry) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No entry with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_cv_entry',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $entry;
    }
}
