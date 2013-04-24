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

namespace CudiBundle\Controller\Prof;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Article,
    CudiBundle\Form\Prof\Subject\Enrollment as EnrollmentForm,
    SyllabusBundle\Entity\StudentEnrollment,
    Zend\View\Model\ViewModel;

/**
 * SubjectController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        if (!($academicYear = $this->getAcademicYear()))
            return new ViewModel();

        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllByProfAndAcademicYear($this->getAuthentication()->getPersonObject(), $this->getAcademicYear());

        return new ViewModel(
            array(
                'subjects' => $subjects,
                'academicYear' => $academicYear,
            )
        );
    }

    public function subjectAction()
    {
        if (!($subject = $this->_getSubject()))
            return new ViewModel();

        if (!($academicYear = $this->getAcademicYear()))
            return new ViewModel();

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
            ->findAllBySubjectAndAcademicYear($subject, $academicYear, true);

        $articleMappings = array();
        foreach($mappings as $mapping) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('mapping', $mapping->getId(), 'remove');

            if (!isset($actions[0]) || $actions[0]->isRefused())
                $articleMappings[] = $mapping;
        }

        $profMappings = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllBySubjectAndAcademicYear($subject, $academicYear);

        $enrollment = $subject->getEnrollment($academicYear);
        $enrollmentForm = new EnrollmentForm($enrollment);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $enrollmentForm->setData($formData);

            if ($enrollmentForm->isValid()) {
                $formData = $enrollmentForm->getFormData($formData);

                if ($enrollment) {
                    $enrollment->setNumber($formData['students']);
                } else {
                    $enrollment = new StudentEnrollment($subject, $academicYear, $formData['students']);
                    $this->getEntityManager()->persist($enrollment);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The student enrollment was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_prof_subject',
                    array(
                        'action' => 'subject',
                        'id' => $subject->getId(),
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'subject' => $subject,
                'academicYear' => $academicYear,
                'articleMappings' => $articleMappings,
                'profMappings' => $profMappings,
                'enrollmentForm' => $enrollmentForm,
            )
        );
    }

    public function typeaheadAction()
    {
        if (!($academicYear = $this->getAcademicYear()))
            return new ViewModel();

        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findAllByNameAndProfAndAcademicYearTypeAhead($this->getParam('string'), $this->getAuthentication()->getPersonObject(), $academicYear);

        $result = array();
        foreach($subjects as $subject) {
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

    private function _getSubject()
    {
        if (!($academicYear = $this->getAcademicYear()))
            return;

        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No ID was given to identify the subject!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $this->getParam('id'),
                $this->getAuthentication()->getPersonObject(),
                $academicYear
            );

        if (null === $mapping) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No subject with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping->getSubject();
    }
}
