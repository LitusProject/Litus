<?php

namespace CudiBundle\Controller\Prof;

use DateInterval;
use Laminas\View\Model\ViewModel;
use SyllabusBundle\Entity\Subject;
use SyllabusBundle\Entity\Subject\ProfMap;
use SyllabusBundle\Entity\Subject\StudentEnrollment;

/**
 * SubjectController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return new ViewModel();
        }

        if ($this->hasAccess()->toResourceAction('cudi_prof_subject', 'all')) {
            $subjects = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findByAcademicYear($this->getCurrentAcademicYear());
        } else {
            $subjects = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findAllByProfAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getCurrentAcademicYear());
        }

        return new ViewModel(
            array(
                'subjects'     => $subjects,
                'academicYear' => $academicYear,
            )
        );
    }

    public function subjectAction()
    {
        $subject = $this->getSubjectEntity();
        if ($subject === null) {
            return new ViewModel();
        }

        $academicYear = $this->getCurrentAcademicYear();

        $articleMappings = $this->getArticlesFromMappings(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllBySubjectAndAcademicYear($subject, $academicYear, true)
        );

        $currentArticles = array();
        foreach ($articleMappings as $mapping) {
            $currentArticles[$mapping['article']->getId()] = $mapping['article']->getId();
        }

        $previous = clone $academicYear->getStartDate();
        $previous->sub(new DateInterval('P1Y'));

        $previousAcademicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($previous);

        $previousArticleMappings = $this->getArticlesFromMappings(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllBySubjectAndAcademicYear($subject, $previousAcademicYear, true)
        );

        foreach ($previousArticleMappings as $key => $mapping) {
            if (isset($currentArticles[$mapping['article']->getId()])) {
                unset($previousArticleMappings[$key]);
            }
        }

        $profMappings = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findAllBySubjectAndAcademicYear($subject, $academicYear);

        $enrollment = $subject->getEnrollment($academicYear);
        $enrollmentForm = $this->getForm(
            'cudi_prof_subject_enrollment',
            array(
                'enrollment' => $enrollment,
            )
        );

        if ($this->getRequest()->isPost()) {
            $enrollmentForm->setData($this->getRequest()->getPost());

            if ($enrollmentForm->isValid()) {
                $formData = $enrollmentForm->getData();

                if ($enrollment) {
                    $enrollment->setNumber($formData['students']);
                } else {
                    $enrollment = new StudentEnrollment($subject, $academicYear, $formData['students']);
                    $this->getEntityManager()->persist($enrollment);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The student enrollment was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_prof_subject',
                    array(
                        'action'   => 'subject',
                        'id'       => $subject->getId(),
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'subject'                 => $subject,
                'academicYear'            => $academicYear,
                'articleMappings'         => $articleMappings,
                'previousArticleMappings' => $previousArticleMappings,
                'profMappings'            => $profMappings,
                'enrollmentForm'          => $enrollmentForm,
            )
        );
    }

    public function typeaheadAction()
    {
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findAllByNameAndProfAndAcademicYearTypeahead($this->getParam('string'), $this->getAuthentication()->getPersonObject(), $academicYear);

        $result = array();
        foreach ($subjects as $subject) {
            $item = (object) array();
            $item->id = $subject->getSubject()->getId();
            $item->value = $subject->getSubject()->getCode() . ' - ' . $subject->getSubject()->getName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function getArticlesFromMappings($mappings)
    {
        $articleMappings = array();
        foreach ($mappings as $mapping) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('mapping', $mapping->getArticle()->getId(), 'remove');

            $edited = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndPreviousIdAndAction('article', $mapping->getArticle()->getId(), 'edit');

            $removed = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('article', $mapping->getArticle()->getId(), 'delete', false);

            if ((!isset($actions[0]) || $actions[0]->isRefused()) && count($removed) == 0) {
                if (isset($edited[0]) && !$edited[0]->isRefused()) {
                    $edited[0]->setEntityManager($this->getEntityManager());
                    $article = $edited[0]->getEntity();
                } else {
                    $article = $mapping->getArticle();
                }

                $articleMappings[] = array(
                    'mapping' => $mapping,
                    'article' => $article,
                );
                $article->setEntityManager($this->getEntityManager());
            }
        }

        return $articleMappings;
    }

    /**
     * @return Subject|null
     */
    private function getSubjectEntity()
    {
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return;
        }

        $mapping = null;

        if ($this->hasAccess()->toResourceAction('cudi_prof_subject', 'all')) {
            $mapping = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findOneBySubjectIdAndAcademicYear(
                    $this->getParam('id', 0),
                    $academicYear
                );
        } else {
            $mapping = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findOneBySubjectIdAndProfAndAcademicYear(
                    $this->getParam('id', 0),
                    $this->getAuthentication()->getPersonObject(),
                    $academicYear
                );
        }

        if (!($mapping instanceof ProfMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action'   => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping->getSubject();
    }
}
