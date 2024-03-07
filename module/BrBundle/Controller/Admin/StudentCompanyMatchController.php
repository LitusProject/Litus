<?php

namespace BrBundle\Controller\Admin;

use CommonBundle\Component\Controller\ActionController\AdminController;
use CommonBundle\Component\Util\AcademicYear;
use Laminas\View\Model\ViewModel;

/**
 * StudentCompanyMatchController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class StudentCompanyMatchController extends AdminController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $student_company_matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\StudentCompanyMatch')
            ->findAllByAcademicYearQuery($academicYear)
            ->getResult();

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears'      => $academicYears,
                'matches' => $student_company_matches,
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear|void
     */
    private function getAcademicYear()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if ($academicYear === null) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_studentcompanymatch',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
