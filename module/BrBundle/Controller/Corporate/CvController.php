<?php

namespace BrBundle\Controller\Corporate;

use BrBundle\Component\Util\Cv;
use CommonBundle\Entity\General\AcademicYear;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * CvController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvController extends \BrBundle\Component\Controller\CorporateController
{
    public function groupedAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYear();
        $onlyArchive = false;

        if (!in_array($academicYear, $person->getCompany()->getCvBookYears())) {
            if ($this->getParam('academicyear') === null
                && count($person->getCompany()->getCvBookYears()) > 0
            ) {
                $this->redirect()->toRoute(
                    'br_corporate_cv',
                    array(
                        'action'       => 'grouped',
                        'academicyear' => $person->getCompany()->getCvBookYears()[count($person->getCompany()->getCvBookYears()) - 1]->getCode(),
                    )
                );

                return new ViewModel();
            } elseif ($this->getParam('academicyear') === null
                && count($person->getCompany()->getCvBookArchiveYears()) > 0
            ) {
                $onlyArchive = true;
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    'You don\'t have access to the CVs of this year.'
                );

                $this->redirect()->toRoute(
                    'br_corporate_index',
                    array(
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        $result = Cv::getGrouped($this->getEntityManager(), $academicYear);

        $gradesMapEnabled = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_grades_map_enabled');

        $gradesMap = unserialize(
            $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_grades_map')
        );

        return new ViewModel(
            array(
                'academicYear'     => $academicYear,
                'gradesMapEnabled' => $gradesMapEnabled,
                'gradesMap'        => $gradesMap,
                'studies'          => $result,
                'onlyArchive'      => $onlyArchive,
                'profilePath'      => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            )
        );
    }

    public function listAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYear();
        $onlyArchive = false;

        if (!in_array($academicYear, $person->getCompany()->getCvBookYears())) {
            if ($this->getParam('academicyear') === null
                && count($person->getCompany()->getCvBookYears()) > 0
            ) {
                $this->redirect()->toRoute(
                    'br_corporate_cv',
                    array(
                        'action'       => 'list',
                        'academicyear' => $person->getCompany()->getCvBookYears()[count($person->getCompany()->getCvBookYears()) - 1]->getCode(),
                        'sortby'       => is_null($this->getParam('sortby')) ?? 'lastname', $this->getParam('sortby'),
                    )
                );

                return new ViewModel();
            } elseif ($this->getParam('academicyear') === null
                && count($person->getCompany()->getCvBookArchiveYears()) > 0
            ) {
                $onlyArchive = true;
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    'You don\'t have access to the CVs of this year.'
                );

                $this->redirect()->toRoute(
                    'br_corporate_index',
                    array(
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        $entries = $this->getList($academicYear, $this->getParam('sortby'));

        $gradesMapEnabled = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_grades_map_enabled');

        $gradesMap = unserialize(
            $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_grades_map')
        );

        return new ViewModel(
            array(
                'academicYear'     => $academicYear,
                'entries'          => $entries,
                'gradesMapEnabled' => $gradesMapEnabled,
                'gradesMap'        => $gradesMap,
                'profilePath'      => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'onlyArchive'      => $onlyArchive,
            )
        );
    }

    public function pdfAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYear();

        if (!in_array($academicYear, $person->getCompany()->getCvBookYears())) {
            if ($this->getParam('academicyear') === null
                && count($person->getCompany()->getCvBookYears()) > 0
            ) {
                $this->redirect()->toRoute(
                    'br_corporate_cv',
                    array(
                        'action'       => 'pdf',
                        'academicyear' => $person->getCompany()->getCvBookYears()[count($person->getCompany()->getCvBookYears()) - 1]->getCode(),
                    )
                );

                return new ViewModel();
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    'You don\'t have access to the CVs of this year.'
                );

                $this->redirect()->toRoute(
                    'br_corporate_index',
                    array(
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        $cvbookPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cvbook_path') . '/';
        $fileName = 'cvbook-' . $academicYear->getCode(true) . '.pdf';

        return new ViewModel(
            array(
                'action'       => 'pdf',
                'academicYear' => $academicYear,
                'filePath'     => $cvbookPath . $fileName,
                'fileName'     => $fileName,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYear();

        if (!in_array($academicYear, $person->getCompany()->getCvBookYears())) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to the CVs of this year.'
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $filters = array();

        if ($this->getParam('string') !== null) {
            $filters['string'] = $this->getParam('string');
        }

        if ($this->getParam('min') !== null || $this->getParam('max') !== null) {
            $filters['grade'] = array();
            if ($this->getParam('min') !== null) {
                $filters['grade']['min'] = $this->getParam('min');
            } else {
                $filters['grade']['min'] = 0;
            }
            if ($this->getParam('max') !== null) {
                $filters['grade']['max'] = $this->getParam('max');
            } else {
                $filters['grade']['max'] = 100;
            }
        }

        $filtered = $this->doFilter($this->getList($academicYear, $this->getParam('sortby')), $filters);
        $result = array();
        foreach ($filtered as $entry) {
            $result[] = $entry->getId();
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function downloadArchiveAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $archive = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_archive_years')
        );

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.file_path') . '/cv/';

        $archiveYearKey = '';
        $archiveYear = null;
        foreach ($archive as $key => $year) {
            if ($year['full_year'] == $this->getParam('academicyear')) {
                $archiveYear = $year;
                $archiveYearKey = $key;
                break;
            }
        }

        if (!in_array($archiveYearKey, $person->getCompany()->getCvBookArchiveYears()) || $archiveYear === null) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to the CVs of this year.'
            );

            $this->redirect()->toRoute(
                'br_corporate_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'inline; filename="cv-' . $archiveYear['full_year'] . '.pdf"',
                'Content-Type'        => 'application/octet-stream',
                'Content-Length'      => filesize($filePath . $archiveYear['file']),
            )
        );
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath . $archiveYear['file'], 'r');
        $data = fread($handle, filesize($filePath . $archiveYear['file']));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    /**
     * @param  AcademicYear $academicYear
     * @param  $sortBy
     * @return array
     */
    private function getList(AcademicYear $academicYear, $sortBy)
    {
        if ($sortBy === 'firstname') {
            return $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByAcademicYearByFirstname($academicYear);
        }
        return $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllByAcademicYearByLastname($academicYear);
    }

    /**
     * @param  array $entries
     * @param  array $filters
     * @return array
     */
    private function doFilter($entries, $filters)
    {
        if (isset($filters['string'])) {
            $entries = $this->filterString($entries, $filters['string']);
        }

        if (isset($filters['grade'])) {
            $entries = $this->filterGrade($entries, $filters['grade']);
        }

        return $entries;
    }

    /**
     * @param  array  $entries
     * @param  string $string
     * @return array
     */
    private function filterString($entries, $string)
    {
        $result = array();
        $words = preg_split('/[\s,]+/', $string);
        foreach ($entries as $entry) {
            $matches = true;
            foreach ($words as $word) {
                if (!(preg_match('/.*' . $word . '.*/i', $entry->getLastName()) || preg_match('/.*' . $word . '.*/i', $entry->getFirstName()))) {
                    $matches = false;
                    break;
                }
            }
            if ($matches) {
                $result[] = $entry;
            }
        }

        return $result;
    }

    /**
     * @param  array   $entries
     * @param  integer $grade
     * @return array
     */
    private function filterGrade($entries, $grade)
    {
        $result = array();
        $min = $grade['min'] * 100;
        $max = $grade['max'] * 100;
        foreach ($entries as $entry) {
            if ($entry->getGrade() >= $min && $entry->getGrade() <= $max) {
                $result[] = $entry;
            }
        }

        return $result;
    }
}
