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
    SyllabusBundle\Entity\Department,
    SyllabusBundle\Entity\StudyDepartmentMap,
    SyllabusBundle\Form\Admin\Department\Add as AddForm,
    SyllabusBundle\Form\Admin\Department\Edit as EditForm,
    SyllabusBundle\Form\Admin\Department\Study\Add as StudyForm,
    Zend\View\Model\ViewModel;

/**
 * DepartmentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DepartmentController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Department')
                ->findAll(),
            $this->getParam('page')
        );

        foreach($paginator as $department)
            $department->setEntityManager($this->getEntityManager());

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
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->persist(new Department($formData['name']));
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The department was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_department',
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
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (!($department = $this->_getDepartment()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $department);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $department->setName($formData['name']);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The department was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_department',
                    array(
                        'action' => 'edit',
                        'id' => $department->getId(),
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
                'department' => $department,
            )
        );
    }

    public function studiesAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (!($department = $this->_getDepartment()))
            return new ViewModel();

        $form = new StudyForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($formData['study_id']);

                $map = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudyDepartmentMap')
                    ->findOneByStudyDepartmentAndAcademicYear($study, $department, $academicYear);

                if (null !== $map) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The department study mapping already existed!'
                        )
                    );
                } else {
                    $this->getEntityManager()->persist(new StudyDepartmentMap($study, $department, $academicYear));

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Succes',
                            'The department study mapping was successfully added!'
                        )
                    );
                }

                $this->redirect()->toRoute(
                    'admin_department',
                    array(
                        'action' => 'studies',
                        'id' => $department->getId(),
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
            ->getRepository('SyllabusBundle\Entity\StudyDepartmentMap')
            ->findAllByDepartmentAndAcademicYear($department, $academicYear);

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
                'department' => $department,
                'studies' => $studies,
            )
        );
    }

    public function deleteStudyAction()
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

    private function _getDepartment()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the department!'
                )
            );

            $this->redirect()->toRoute(
                'admin_department',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $department = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Department')
            ->findOneById($this->getParam('id'));

        if (null === $department) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No department with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_department',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $department->setEntityManager($this->getEntityManager());

        return $department;
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
                'admin_department',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\StudyDepartmentMap')
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
                'admin_department',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $mapping;
    }
}
