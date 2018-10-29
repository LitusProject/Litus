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

namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use SyllabusBundle\Entity\Group;
use SyllabusBundle\Entity\Poc;
use Zend\View\Model\ViewModel;

/**
 * PocController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PocController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }
        $pocsWithIndicator = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Poc')
                ->findAllPocsWithIndicatorByAcademicYear($academicYear);
        $groups = array();
        foreach ($pocsWithIndicator as $poc) {
            $poc->getGroupId()->setEntityManager($this->getEntityManager());
            $groups[] = $poc->getGroupId();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(

                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'groups'              => $groups,
            )
        );
    }

    public function membersAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }
        if (!($pocgroup = $this->getGroupEntity())) {
            return new ViewModel();
        }
        $form = $this->getForm('syllabus_poc_add', array('pocgroup' => $pocgroup));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $poc = $form->hydrateObject();
                $poc->setAcademicYear($academicYear);
                $poc->setGroupId($pocgroup);

                $this->getEntityManager()->persist($poc);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The pocer was successfully added to ' . $pocgroup->getName() . '!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_poc',
                    array(
                        'action'       => 'members',
                        'academicyear' => $academicYear->getCode(),
                        'id'           => $pocgroup->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $pocers = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findPocersFromGroupAndAcademicYear($pocgroup, $academicYear);

        return new ViewModel(
            array(
                'form'     => $form,
                'pocgroup' => $pocgroup,
                'pocers'   => $pocers,
            )
        );
    }

    public function editEmailAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }
        if (!($pocgroup = $this->getGroupEntity())) {
            return new ViewModel();
        }
        $pocIndicator = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findIndicatorFromGroupAndAcademicYear($pocgroup, $academicYear);

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

    /**
     * deletes the pocgroup
     */
    public function deleteAction()
    {
        if (!($academicYear = $this->getAcademicYearEntity())) {
            return new ViewModel();
        }
        $this->initAjax();
        if (!($pocgroup = $this->getGroupEntity())) {
            return new ViewModel();
        }
        $pocs = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findPocersFromGroupAndAcademicYearWithIndicator($pocgroup, $academicYear);
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
        if (!($poc = $this->getPocEntity())) {
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
     * @return Study|null
     */
    private function getPocEntity()
    {
        $poc = $this->getEntityById('SyllabusBundle\Entity\Poc');

        if (!($poc instanceof Poc)) {
            $this->flashMessenger()->error(
                'Error',
                'No poc was found!'
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
     * @return Study|null
     */
    private function getGroupEntity()
    {
        $pocgroup = $this->getEntityById('SyllabusBundle\Entity\Group');

        if (!($pocgroup instanceof Group)) {
            $this->flashMessenger()->error(
                'Error',
                'No pocgroup was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_poc',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $pocgroup;
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
