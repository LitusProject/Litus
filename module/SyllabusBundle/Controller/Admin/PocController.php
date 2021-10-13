<?php

namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use Laminas\View\Model\ViewModel;
use SyllabusBundle\Entity\Group;
use SyllabusBundle\Entity\Poc;

/**
 * PocController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PocController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $pocsWithIndicator = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findAllPocsWithIndicatorByAcademicYear($academicYear);

        $pocGroups = array();
        foreach ($pocsWithIndicator as $poc) {
            $poc->getGroupId()->setEntityManager($this->getEntityManager());
            $pocGroups[] = $poc->getGroupId();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(

                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'pocGroups'           => $pocGroups,
            )
        );
    }

    public function membersAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $pocGroup = $this->getGroupEntity();
        if ($pocGroup === null) {
            return new ViewModel();
        }

        $form = $this->getForm('syllabus_poc_add', array('poc_group' => $pocGroup));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $poc = $form->hydrateObject();
                $poc->setAcademicYear($academicYear);
                $poc->setGroupId($pocGroup);

                $this->getEntityManager()->persist($poc);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The POC\'er was successfully added to ' . $pocGroup->getName() . '!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_poc',
                    array(
                        'action'       => 'members',
                        'academicyear' => $academicYear->getCode(),
                        'id'           => $pocGroup->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $pocers = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findPocersFromGroupAndAcademicYear($pocGroup, $academicYear);

        return new ViewModel(
            array(
                'form'     => $form,
                'pocGroup' => $pocGroup,
                'pocers'   => $pocers,
            )
        );
    }

    public function editEmailAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $pocGroup = $this->getGroupEntity();
        if ($pocGroup === null) {
            return new ViewModel();
        }

        $pocIndicator = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findIndicatorFromGroupAndAcademicYear($pocGroup, $academicYear);

        $form = $this->getForm('syllabus_poc_editEmail', array('poc' => $pocIndicator));
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $pocIndicator->setEmailAdress($data['emailAdress']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The email Adress was successfully updated!'
                );
                $this->redirect()->toRoute(
                    'syllabus_admin_poc',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'         => $form,
                'pocIndicator' => $pocIndicator,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $pocGroup = $this->getGroupEntity();
        if ($pocGroup === null) {
            return new ViewModel();
        }

        $pocs = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findPocersFromGroupAndAcademicYearWithIndicator($pocGroup, $academicYear);
        foreach ($pocs as $poc) {
            $this->getEntityManager()->remove($poc);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteMemberAction()
    {
        $this->initAjax();

        $poc = $this->getPocEntity();
        if ($poc === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($poc);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \SyllabusBundle\Entity\Poc|null
     */
    private function getPocEntity()
    {
        $poc = $this->getEntityById('SyllabusBundle\Entity\Poc');

        if (!($poc instanceof Poc)) {
            $this->flashMessenger()->error(
                'Error',
                'No POC was found!'
            );
            $this->redirect()->toRoute(
                'syllabus_admin_poc',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $poc;
    }

    /**
     * @return \SyllabusBundle\Entity\Group|null
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
                'syllabus_admin_poc',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $group;
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
                'syllabus_admin_poc',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
