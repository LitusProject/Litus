<?php

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\Person\Organization\UnitMap\Academic as UnitMapAcademic;
use CommonBundle\Entity\User\Person\Organization\UnitMap\External as UnitMapExternal;
use Laminas\View\Model\ViewModel;

class WorkingGroupController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                ->findAllWorkgroupMembersByAcademicYearQuery($academicYear),
            $this->getParam('page'),
        );

        return new ViewModel(
            array(
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
                'activeAcademicYear' => $academicYear,
                'academicYears'      => $academicYears,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $unitMap = $this->getUnitMapEntity();
        if ($unitMap === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($unitMap);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        $academic = $this->getEntityById('CommonBundle\Entity\User\Person\Academic');

        if (!($academic instanceof Academic)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_working_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academic;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYearEntity()
    {
        if ($this->getParam('academicyear') === null) {
            return $this->getCurrentAcademicYear();
        }

        $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (!($academicYear instanceof AcademicYearEntity)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_working_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * @return \CommonBundle\Entity\User\Person\Organization\UnitMap|null
     */
    private function getUnitMapEntity()
    {
        $unitMap = $this->getEntityById('CommonBundle\Entity\User\Person\Organization\UnitMap');

        if (!($unitMap instanceof UnitMapAcademic || $unitMap instanceof UnitMapExternal)) {
            $this->flashMessenger()->error(
                'Error',
                'No unit map was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_working_group',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $unitMap;
    }
}
