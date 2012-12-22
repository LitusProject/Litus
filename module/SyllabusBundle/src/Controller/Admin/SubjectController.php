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
