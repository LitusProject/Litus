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
    DateInterval,
    DateTime,
    SyllabusBundle\Entity\Group,
    SyllabusBundle\Entity\StudyGroupMap,
    SyllabusBundle\Form\Admin\Group\Add as AddForm,
    SyllabusBundle\Form\Admin\Group\Edit as EditForm,
    SyllabusBundle\Form\Admin\Group\Study\Add as StudyForm,
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
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromEntity(
            'SyllabusBundle\Entity\Group',
            $this->getParam('page'),
            array(
                'removed' => false,
            )
        );

        foreach($paginator as $group)
            $group->setEntityManager($this->getEntityManager());

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
                $formData = $form->getFormData($formData);

                $extraMembers = preg_split("/[,;\s]+/", $formData['extra_members']);
                $excludedMembers = preg_split("/[,;\s]+/", $formData['excluded_members']);

                $this->getEntityManager()->persist(new Group($formData['name'], $formData['cvbook'], serialize($extraMembers), serialize($excludedMembers)));
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The group was successfully added!'
                    )
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
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (!($group = $this->_getGroup()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $group);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $extraMembers = preg_split("/[,;\s]+/", $formData['extra_members']);
                $excludedMembers = preg_split("/[,;\s]+/", $formData['excluded_members']);

                $group->setName($formData['name'])
                    ->setCvBook($formData['cvbook'])
                    ->setExtraMembers(serialize($extraMembers))
                    ->setExcludedMembers(serialize($excludedMembers));
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The group was successfully updated!'
                    )
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
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        if (!($group = $this->_getGroup()))
            return new ViewModel();

        $form = new StudyForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $study = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($formData['study_id']);

                $map = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\StudyGroupMap')
                    ->findOneByStudyGroupAndAcademicYear($study, $group, $academicYear);

                if (null !== $map) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The group study mapping already existed!'
                        )
                    );
                } else {
                    $this->getEntityManager()->persist(new StudyGroupMap($study, $group, $academicYear));

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Succes',
                            'The group study mapping was successfully added!'
                        )
                    );
                }

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
            ->getRepository('SyllabusBundle\Entity\StudyGroupMap')
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

        if (!($group = $this->_getGroup()))
            return new ViewModel();

        $group->setRemoved();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
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
            $startAcademicYear = AcademicYear::getStartOfAcademicYear();

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );

            $next = clone $start;
            $next->add(new DateInterval('P1Y'));
            if ($next <= new DateTime())
                $start = $next;
        } else {
            $startAcademicYear = AcademicYear::getDateTime($this->getParam('academicyear'));

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );
        }
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
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

    private function _getGroup()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the group!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $group = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findOneById($this->getParam('id'));

        if (null === $group) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No group with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $group->setEntityManager($this->getEntityManager());

        return $group;
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
                'syllabus_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\StudyGroupMap')
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
                'syllabus_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $mapping;
    }
}
