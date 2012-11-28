<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    SyllabusBundle\Entity\Study,
    Zend\View\Model\ViewModel;

/**
 * SubjectController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($study = $this->_getStudy()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (null !== $this->getParam('field'))
            $mappings = $this->_search($study, $academicYear);

        if (!isset($mappings)) {
            $mappings = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                ->findAllByStudyAndAcademicYear($study, $academicYear);
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'study' => $study,
                'mappings' => $mappings,
                'currentAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
            )
        );
    }

    public function subjectAction()
    {
        if (!($subject = $this->_getSubject()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $profs = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllBySubjectAndAcademicYear($subject, $academicYear);

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
            ->findAllBySubjectAndAcademicYear($subject, $academicYear);

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'subject' => $subject,
                'profMappings' => $profs,
                'articleMappings' => $articles,
                'currentAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        if (!($study = $this->_getStudy()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $subjects = $this->_search($study, $academicYear);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($subjects, $numResults);

        $result = array();
        foreach($subjects as $subject) {
            $item = (object) array();
            $item->id = $subject->getSubject()->getId();
            $item->name = $subject->getSubject()->getName();
            $item->code = $subject->getSubject()->getCode();
            $item->semester = $subject->getSubject()->getSemester();
            $item->credits = $subject->getSubject()->getCredits();
            $item->mandatory = $subject->isMandatory();
            $item->students = $subject->getSubject()->getNbEnrollment($academicYear);
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function typeaheadAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
            return;

        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findAllByNameAndAcademicYearTypeAhead($this->getParam('string'), $academicYear);

        $result = array();
        foreach($subjects as $subject) {
            $item = (object) array();
            $item->id = $subject->getId();
            $item->value = $subject->getCode() . ' - ' . $subject->getName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _search(Study $study, AcademicYearEntity $academicYear)
    {
        switch($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByNameAndStudyAndAcademicYear($this->getParam('string'), $study, $academicYear);
            case 'code':
                return $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                    ->findAllByCodeAndStudyAndAcademicYear($this->getParam('string'), $study, $academicYear);
        }
    }

    private function _getStudy()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the study!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $study = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findOneById($this->getParam('id'));

        if (null === $study) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No study with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $study;
    }

    private function _getSubject()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the subject!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $study = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneById($this->getParam('id'));

        if (null === $study) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No subject with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $study;
    }

    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
            $start = AcademicYear::getStartOfAcademicYear();
        } else {
            $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }
}
