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
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    SyllabusBundle\Component\Document\Generator\Group as CsvGenerator,
    SyllabusBundle\Entity\Group,
    SyllabusBundle\Entity\Group\StudyMap,
    Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromEntity(
            'SyllabusBundle\Entity\Group',
            $this->getParam('page'),
            array(
                'removed' => false,
            )
        );

        foreach ($paginator as $group) {
            $group->setEntityManager($this->getEntityManager());
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('syllabus_group_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist($form->hydrateObject());
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The group was successfully added!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_group',
                    array(
                        'action' => 'manage',
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (!($group = $this->getGroupEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('syllabus_group_edit', array('group' => $group));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The group was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_group',
                    array(
                        'action' => 'edit',
                        'id' => $group->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
                'group' => $group,
            )
        );
    }

    public function studiesAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (!($group = $this->getGroupEntity())) {
            return new ViewModel();
        }

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllByAcademicYear($academicYear);

        $form = $this->getForm('syllabus_group_study_add', array('studies' => $studies));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $studyIds = $formData['studies'];

                if ($studyIds) {
                    foreach ($studyIds as $studyId) {
                        $study = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Study')
                            ->findOneById($studyId);

                        $map = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
                            ->findOneByStudyGroup($study, $group);

                        if (null === $map) {
                            $this->getEntityManager()->persist(new StudyMap($study, $group));
                        }
                    }
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'No studies were selected to add to the group!'
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The group study mapping was successfully added!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_group',
                    array(
                        'action' => 'studies',
                        'id' => $group->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel();
            }
        }

        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
            ->findAllByGroupAndAcademicYear($group, $academicYear);

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
                'group' => $group,
                'studies' => $studies,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($group = $this->getGroupEntity())) {
            return new ViewModel();
        }

        $group->setRemoved();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteStudyAction()
    {
        $this->initAjax();

        if (!($mapping = $this->getStudyMapEntity())) {
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

    public function exportAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }

        if (!($group = $this->getGroupEntity())) {
            return new ViewModel();
        }

        $exportFile = new CsvFile();
        $csvGenerator = new CsvGenerator($this->getEntityManager(), $group, $academicYear);
        $csvGenerator->generateDocument($exportFile);

        $this->getResponse()->getHeaders()
            ->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $group->getName() . '_' . $academicYear->getCode() . '.csv"',
            'Content-Type' => 'text/csv',
        ));

        return new ViewModel(
            array(
                'result' => $exportFile->getContent(),
            )
        );
    }

    /**
     * @return Group|null
     */
    private function getGroupEntity()
    {
        $group = $this->getEntityById('SyllabusBundle\Entity\Group');

        if (!($group instanceof Group)) {
            $this->flashMessenger()->error(
                'Error',
                'No group was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $group->setEntityManager($this->getEntityManager());

        return $group;
    }

    /**
     * @return StudyMap|null
     */
    private function getStudyMapEntity()
    {
        $map = $this->getEntityById('SyllabusBundle\Entity\Group\StudyMap');

        if (!($map instanceof StudyMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No study group map was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_group',
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
                'syllabus_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
