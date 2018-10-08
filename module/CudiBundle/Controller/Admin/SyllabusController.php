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

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    Cudibundle\Entity\Article,
    SyllabusBundle\Entity\Study,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class SyllabusController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
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
        if (!($study = $this->getStudyEntity())) {
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
        if (!($study = $this->getStudyEntity())) {
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
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $study->getTitle() . '_books.csv"',
            'Content-Type'        => 'text/csv',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    /**
     * @param  AcademicYearEntity       $academicYear
     * @return \Doctrine\ORM\Query|null
     */
    private function searchStudies(AcademicYearEntity $academicYear)
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
