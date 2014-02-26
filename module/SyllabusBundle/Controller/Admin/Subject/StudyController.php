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

namespace SyllabusBundle\Controller\Admin\Subject;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    SyllabusBundle\Entity\StudySubjectMap,
    SyllabusBundle\Form\Admin\Subject\Study\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * StudyController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StudyController extends \CudiBundle\Component\Controller\ActionController
{
    public function addAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (!($subject = $this->_getSubject()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager(), $subject, $academicYear);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($formData['study_id']);

                $mapping = new StudySubjectMap($study, $subject, $formData['mandatory'], $academicYear);
                $this->getEntityManager()->persist($mapping);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The study mapping was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_subject',
                    array(
                        'action' => 'edit',
                        'id' => $subject->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );
            }
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'subject' => $subject,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (!($mapping = $this->_getMapping()))
            return new ViewModel();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->_getMapping()))
            return new ViewModel();

        $this->getEntityManager()->remove($mapping);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
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
                'syllabus_admin_subject',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $subject = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneById($this->getParam('id'));

        if (null === $subject) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No subject with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $subject;
    }

    private function _getMapping()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the mapping!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
            ->findOneById($this->getParam('id'));

        if (null === $mapping) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No mapping with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $mapping;
    }

    private function _getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear'))
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }
}
