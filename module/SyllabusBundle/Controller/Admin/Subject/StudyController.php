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

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    SyllabusBundle\Entity\Study\SubjectMap,
    SyllabusBundle\Entity\Subject,
    Zend\View\Model\ViewModel;

/**
 * StudyController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StudyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function addAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (!($subject = $this->getSubjectEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'syllabus_subject_study_add',
            array(
                'subject'       => $subject,
                'academic_year' => $academicYear,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($formData['study']['id']);

                $mapping = $form->hydrateObject(
                    new SubjectMap($study, $subject, false, $academicYear)
                );
                $this->getEntityManager()->persist($mapping);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The study mapping was successfully added!'
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
        if (!($mapping = $this->getSubjectMapEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('syllabus_subject_study_edit', array('mapping' => $mapping));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The study mapping was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_subject',
                    array(
                        'action' => 'edit',
                        'id' => $mapping->getSubject()->getId(),
                        'academicyear' => $mapping->getAcademicYear()->getCode(),
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
                'currentAcademicYear' => $mapping->getAcademicYear(),
                'mapping' => $mapping,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->getSubjectMapEntity())) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($mapping);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Subject|null
     */
    private function getSubjectEntity()
    {
        $subject = $this->getEntityById('SyllabusBundle\Entity\Subject');

        if (!($subject instanceof Subject)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $subject;
    }

    /**
     * @return SubjectMap|null
     */
    private function getSubjectMapEntity()
    {
        $map = $this->getEntityById('SyllabusBundle\Entity\Study\SubjectMap');

        if (!($map instanceof SubjectMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject mapping was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $map;
    }

    /**
     * @return AcademicYearEntity|null
     */
    private function getAcademicYearEntity()
    {
        $date = null;
        if (null !== $this->getParam('academicyear')) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (!($academicYear instanceof AcademicYearEntity)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject_comment',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
