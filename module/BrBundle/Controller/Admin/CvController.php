<?php

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
            ->getRepository('CommonBundle\Entity\General\Config')
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

    public function exportCvCsvAction()
    {
        $translator = $this->getTranslator();
        $locale = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_language');
        $translator->setLocale($locale);

        $entries = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllByAcademicYear($this->getAcademicYear());

        $file = new CsvFile();
        $heading = array('First Name', 'Last Name', 'birthday', 'Email', 'Phone', 'img', 'Study', 'street', 'nr', 'postal', 'city', 'country', 'about', 'grade', 'priorGrade', 'SS_start_master', 'SS_end_master', 'SS_percentage_master', 'SS_title_master', 'SS_start_bach', 'SS_end_bach', 'SS_percentage_bach', 'SS_title_bach', 'additional_diplomas', 'SE_location', 'SE_period', 'L_name', 'L_oral', 'L_written', 'L_extra', 'ComputerSkills', 'Experiences', 'EXP_type', 'EXP_function', 'EXP_start', 'EXP_end', 'thesis', 'futureInterest', 'mobilityEU', 'mobilityWorld','careerExpectations', 'hobbies');

        $picturePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path');
        $monthsEnglish = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
        $monthsDutch = array('Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December' );


        $results = array();
        foreach ($entries as $entry) {
            $birthday = $entry->getBirthDay()->format('d F Y');
            $birthday = str_ireplace($monthsEnglish, $monthsDutch, $birthday);
            $phoneNumber = $entry->getPhoneNumber();

            //Grades may be 0 in the database
            $masterGrade = (string) ($entry->getGrade() / 100);
            if ($entry->getGrade() == 0) {
                $masterGrade = '-';
            }
            $bachelorGrade = (string) ($entry->getPriorGrade() / 100);
            if ($entry->getPriorGrade() == 0) {
                $bachelorGrade = '-';
            }
            $mail = str_replace('vtk.be.test-google-a.com', 'vtk.be', $entry->getEmail());

            //languages
            $lang = '';
            $langOral = '';
            $langWritten = '';
            foreach ($entry->getLanguages() as $l) {
                $lang .= $translator->translate($l->getName()) . ';';
                $langOral .= $translator->translate($l->getOralSkill()) . ';';
                $langWritten .= $translator->translate($l->getWrittenSkill()) . ';';
            }

            //experiences
            $expTypes = '';
            $expFunctions = '';
            $expStarts = '';
            $expEnds = '';
            foreach ($entry->getExperiences() as $e) {
                $expTypes .= $translator->translate($e->getType()) . ';';
                $expFunctions .= $e->getFunction() . ';';
                $expStarts .= strval($e->getStartYear()) . ';';
                $expEnds .= strval($e->getEndYear()) . ';';
            }

            $results[] = array(
                $entry->getFirstName(),
                $entry->getLastName(),
                $birthday,
                $mail,
                substr($phoneNumber, 0, 3) . ' (0)' . substr($phoneNumber, 3, 3) . ' ' . substr($phoneNumber, 6, 2) . ' ' . substr($phoneNumber, 8, 2) . ' ' . substr($phoneNumber, 10, 2),
                $picturePath . '/' . $entry->getAcademic()->getPhotoPath(),
                $entry->getStudy()->getTitle(),
                $entry->getAddress()->getStreet(),
                $entry->getAddress()->getNumber(),
                $entry->getAddress()->getPostal(),
                $entry->getAddress()->getCity(),
                $entry->getAddress()->getCountry(),
                $entry->getAbout(),
                $entry->getGrade(),
                $entry->getPriorGrade(),
                (string) $entry->getMasterStart(),
                (string) $entry->getMasterEnd(),
                $masterGrade,
                $entry->getStudy()->getTitle(),
                (string) $entry->getBachelorStart(),
                (string) $entry->getBachelorEnd(),
                $bachelorGrade,
                $entry->getPriorStudy(),
                $entry->getAdditionalDiplomas(),
                $entry->getErasmusLocation(),
                $entry->getErasmusPeriod(),
                $lang,
                $langOral,
                $langWritten,
                $entry->getLanguageExtra(),
                $entry->getComputerSkills(),
                $expTypes,
                $expFunctions,
                $expStarts,
                $expEnds,
                $entry->getThesisSummary(),
                $entry->getFutureInterest(),
                $translator->translate($entry->getMobilityEurope()),
                $translator->translate($entry->getMobilityWorld()),
                $entry->getCareerExpectations(),
                $entry->getHobbies(),
            );
            if ($entry->getAddress()->getMailbox() !== null && $entry->getAddress()->getMailbox() !== '') {
                $results['bus'] = $entry->getAddress()->getMailbox();
            }
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="academicsCV.csv"',
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
     * Recreates the cv-book pdf saved on the server to make it up to date. This pdf can be viewed by companies.
     *
     * @return ViewModel
     */
    public function synchronizeAction()
    {
        $tmpFile = new TmpFile();
        $year = $this->getAcademicYear();

        $translator = $this->getTranslator();
        $locale = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_language');
        $translator->setLocale($locale);

        $document = new CvBookGenerator($this->getEntityManager(), $year, $tmpFile, $translator);
        $document->generate();

        $filePath = './public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cvbook_path') . '/';
        $file = fopen($filePath . 'cvbook-' . $year->getCode(true) . '.pdf', 'w');
        $result = fwrite($file, $tmpFile->getContent());  // will return false if failed

        if (!$result) {
            $this->flashMessenger()->error(
                'Error',
                'Something went wrong, the cv-book could not be synchronized!'
            );
        } else {
            $this->flashMessenger()->success(
                'Success',
                'The cv-book is now synchronized!'
            );
        }

        $this->redirect()->toRoute(
            'br_admin_cv_entry',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
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
