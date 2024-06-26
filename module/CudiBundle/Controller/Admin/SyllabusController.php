<?php

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use CommonBundle\Entity\General\AcademicYear;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use SyllabusBundle\Entity\Study;

/**
 * ArticleController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class SyllabusController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        if ($this->getParam('field') !== null) {
            $studies = $this->searchStudies($academicYear);
        }

        if (!isset($studies)) {
            $studies = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study')
                ->findAllByAcademicYearQuery($academicYear);
        }

        $paginator = $this->paginator()->createFromQuery(
            $studies,
            $this->getParam('page')
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(true),
            )
        );
    }

    public function listAction()
    {
        $study = $this->getStudyEntity();
        if ($study === null) {
            return new ViewModel();
        }

        $subject_mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByStudyAndAcademicYearQuery($study);

        $totalCost = 0;
        $totalCostMandatoryOnly = 0;
        foreach ($subject_mappings as $sm) {
            $sm->getArticle()->setEntityManager($this->getEntityManager());
            $totalCost += $sm->getArticle()->getSaleArticle()->getSellPrice();
            if ($sm->isMandatory()) {
                $totalCostMandatoryOnly += $sm->getArticle()->getSaleArticle()->getSellPrice();
            }
        }

        return new ViewModel(
            array(
                'study'                  => $study,
                'subject_mappings'       => $subject_mappings,
                'totalCost'              => $totalCost,
                'totalCostMandatoryOnly' => $totalCostMandatoryOnly,
            )
        );
    }

    public function articlescsvAction()
    {
        $study = $this->getStudyEntity();
        if ($study === null) {
            return new ViewModel();
        }

        $file = new CsvFile();
        $heading = array('CourseId', 'TextbookName', 'ISBN', 'Mandatory');

        $study_subject_maps = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
            ->findAllByStudyQuery($study)
            ->getResult();

        $results = array();
        foreach ($study_subject_maps as $ssm) {
            if (!$ssm->isMandatory()) {
                continue;
            }

            $subject_article_maps = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllBySubjectAndAcademicYearQuery($ssm->getSubject(), $study->getAcademicYear())
                ->getResult();

            foreach ($subject_article_maps as $sam) {
                $results[] = array(
                    $ssm->getSubject()->getCode(),
                    $sam->getArticle()->getTitle(),
                    $sam->getArticle()->getIsbn(),
                    $sam->isMandatory() ? 'X' : ' ',
                );
            }
        }

        $document = new CsvGenerator($heading, $results);
        $document->generateDocument($file);

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $study->getTitle() . '_books.csv"',
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

    public function typeaheadAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return;
        }

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $academicYear)
            ->setMaxResults(20)
            ->getResult();

        $result = array();
        foreach ($studies as $study) {
            $item = (object) array();
            $item->id = $study->getId();
            $item->value = 'Phase ' . $study->getPhase() . '&mdash;' . $study->getTitle();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $studies = $this->search($academicYear)
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($studies as $study) {
            $item = (object) array();
            $item->id = $study->getId();
            $item->title = $study->getTitle();
            $item->phase = $study->getPhase();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @param  \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function search(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $academicYear);
        }
    }

    /**
     * @param  \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function searchStudies(AcademicYear $academicYear)
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $academicYear);
        }
    }

    /**
     * @return Study|null
     */
    private function getStudyEntity()
    {
        $study = $this->getEntityById('SyllabusBundle\Entity\Study');

        if (!($study instanceof Study)) {
            $this->flashMessenger()->error(
                'Error',
                'No study was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $study;
    }
}
