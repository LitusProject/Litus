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

namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    SyllabusBundle\Entity\SubjectProfMap,
    SyllabusBundle\Form\Admin\Prof\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * ProfController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function addAction()
    {
        if (!($subject = $this->_getSubject()))
            return new ViewModel();

        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $form = new AddForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $docent = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['prof_id']);

                $mapping = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                    ->findOneBySubjectAndProfAndAcademicYear($subject, $docent, $academicYear);

                if (null === $mapping) {
                    $mapping = new SubjectProfMap($subject, $docent, $academicYear);
                    $this->getEntityManager()->persist($mapping);
                    $this->getEntityManager()->flush();
                }

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The docent was successfully added!'
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

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'subject' => $subject,
                'form' => $form,
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

    public function typeaheadAction()
    {
        $docents = array_merge(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByName($this->getParam('string')),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findAllByUniversityIdentification($this->getParam('string'))
        );

        $result = array();
        foreach ($docents as $docent) {
            $item = (object) array();
            $item->id = $docent->getId();
            $item->value = $docent->getUniversityIdentification() . ' - ' . $docent->getFullName();
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
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the subject!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
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
            $this->flashMessenger()->error(
                'Error',
                'No subject with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $study;
    }

    private function _getMapping()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the mapping!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneById($this->getParam('id'));

        if (null === $mapping) {
            $this->flashMessenger()->error(
                'Error',
                'No mapping with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
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
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }
}
