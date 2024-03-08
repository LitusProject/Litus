<?php

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\StudentCompanyMatch;
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
                'academicYears' => $academicYears,
                'matches' => $student_company_matches,
            )
        );
    }

    public function addAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person == null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_match_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $academic = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['person']['id']);

                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']['id']);

                $student_company_match = new StudentCompanyMatch($company, $academic, $this->getCurrentAcademicYear());

                $this->getEntityManager()->persist($student_company_match);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The match was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_studentcompanymatch',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $student_company_match = $this->getMatchEntity();
        if ($student_company_match === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_match_edit', array('studentCompanyMatch' => $student_company_match));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $academic = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['person']['id']);

                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']['id']);

                $student_company_match
                    ->setAcademic($academic)
                    ->setCompany($company);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The match was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'br_admin_studentcompanymatch',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'match' => $student_company_match,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $student_company_match = $this->getMatchEntity();
        if ($student_company_match === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($student_company_match);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
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

    /**
     * @return StudentCompanyMatch|null
     */
    private function getMatchEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\StudentCompanyMatch');
        if (!($event instanceof StudentCompanyMatch)) {
            $this->flashMessenger()->error(
                'Error',
                'No Match was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_studentcompanymatch',
                array(
                    'action' => 'manage',
                )
            );
            return null;
        }

        return $event;
    }
}
