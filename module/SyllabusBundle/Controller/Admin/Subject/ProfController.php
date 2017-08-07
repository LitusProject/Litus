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

namespace SyllabusBundle\Controller\Admin\Subject;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    SyllabusBundle\Entity\Subject,
    SyllabusBundle\Entity\Subject\ProfMap,
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
        if (!($subject = $this->getSubjectEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $form = $this->getForm('syllabus_subject_prof_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $docent = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['prof']['id']);

                $mapping = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                    ->findOneBySubjectAndProfAndAcademicYear($subject, $docent, $academicYear);

                if (null === $mapping) {
                    $mapping = new ProfMap($subject, $docent, $academicYear);
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
                        'action' => 'view',
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

        if (!($mapping = $this->getProfMapEntity())) {
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

    public function typeaheadAction()
    {
        $docents = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findAllByNameQuery($this->getParam('string'))
            ->setMaxResults(20)
            ->getResult();

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
                'syllabus_admin_study',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $subject;
    }

    /**
     * @return ProfMap|null
     */
    private function getProfMapEntity()
    {
        $map = $this->getEntityById('SyllabusBundle\Entity\Subject\ProfMap');

        if (!($map instanceof ProfMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No prof map was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
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
                'syllabus_admin_study',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
