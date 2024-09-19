<?php

namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use Laminas\View\Model\ViewModel;
use SyllabusBundle\Component\Document\Generator\Group as CsvGenerator;
use SyllabusBundle\Entity\Group;
use SyllabusBundle\Entity\Group\StudyMap;
use SyllabusBundle\Entity\Poc as PocEntity;

/**
 * GroupController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromEntity(
            'SyllabusBundle\Entity\Group',
            $this->getParam('page'),
            array(
                'removed' => false,
            ),
            array(
                'name' => 'ASC',
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
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
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
                        'action'       => 'manage',
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
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form'                => $form,
            )
        );
    }

    public function addPocGroup(Group $groupEntity, AcademicYearEntity $academicYear)
    {
        $object = new PocEntity();
        $object->setAcademicYear($academicYear);
        $object->setGroupId($groupEntity);
        $object->setIndicator(1);
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }

    public function editAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $group = $this->getGroupEntity();
        if ($group === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'syllabus_group_edit',
            array(
                'group'        => $group,
                'academicYear' => $academicYear,
                'isPocGroup'   => $group->getIsPocGroup($academicYear),
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                if ($data['poc_group']) {
                    if (!$group->getIsPocGroup($academicYear)) {
                        $this->addPocGroup($group, $academicYear);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The group was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_group',
                    array(
                        'action'       => 'edit',
                        'id'           => $group->getId(),
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
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form'                => $form,
                'group'               => $group,
            )
        );
    }

    public function studiesAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $group = $this->getGroupEntity();
        if ($group === null) {
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

                        if ($map === null) {
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
                        'action'       => 'studies',
                        'id'           => $group->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $academicYear,
                    )
                );
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
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form'                => $form,
                'group'               => $group,
                'studies'             => $studies,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $group = $this->getGroupEntity();
        if ($group === null) {
            return new ViewModel();
        }

        $group->remove();
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

        $mapping = $this->getStudyMapEntity();
        if ($mapping === null) {
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
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $group = $this->getGroupEntity();
        if ($group === null) {
            return new ViewModel();
        }

        $exportFile = new CsvFile();
        $csvGenerator = new CsvGenerator($this->getEntityManager(), $group, $academicYear);
        $csvGenerator->generateDocument($exportFile);

        $this->getResponse()->getHeaders()
            ->addHeaders(
                array(
                    'Content-Disposition' => 'attachment; filename="' . $group->getName() . '_' . $academicYear->getCode() . '.csv"',
                    'Content-Type'        => 'text/csv',
                )
            );

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
        if ($this->getParam('academicyear') !== null) {
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
