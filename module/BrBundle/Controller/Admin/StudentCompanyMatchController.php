<?php

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Company;
use BrBundle\Entity\StudentCompanyMatch;
use CommonBundle\Component\Controller\ActionController\AdminController;
use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Query;
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

        $paginator = null;
        if ($this->getParam('field') !== null) {
            $matches = $this->search();
            if ($matches === null) {
                return new ViewModel();
            }

            $paginator = $this->paginator()->createFromQuery(
                $matches,
                $this->getParam('page')
            );
        }

        if (is_null($paginator)) {
            $student_company_matches = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\StudentCompanyMatch')
                ->findAllByAcademicYearQuery($academicYear);

            $paginator = $this->paginator()->createFromQuery($student_company_matches, $this->getParam('page'));
        }

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
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

                $categories = $formData['categories'];

                if ($categories != '') {
                    $categories = explode(';', $categories);
                } else {
                    $categories = array();
                }

                $student_company_match = new StudentCompanyMatch($company, $academic, $this->getCurrentAcademicYear(), $categories);

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

                $categories = explode(';', $formData['categories']);

                $student_company_match
                    ->setAcademic($academic)
                    ->setCompany($company)
                    ->setCategories($categories);

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

    public function csvAction()
    {
        $form = $this->getForm('br_match_csv');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $fileData = $this->getRequest()->getFiles();

            $fileName = $fileData['file']['tmp_name'];

            $matchData = array();

            $open = fopen($fileName, 'r');
            if ($open) {
                $data = fgetcsv($open, 10000, ',');

                while ($data !== false) {
                    $matchData[] = $data;
                    $data = fgetcsv($open, 10000, ',');
                }
                fclose($open);
            }

            $form->setData($formData);

            if ($form->isValid()) {
                $count = 0;

                foreach ($matchData as $data) {
                    $rnumber = $data[0];
                    $company_name = $data[1];
                    $categories = $data[2] ?? array();

                    if ($categories != '') {
                        $categories = explode(';', $categories);
                    }

                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUsername($rnumber);

                    if (is_null($academic)) {
                        continue;
                    }

                    $company = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Company')
                        ->findOneBy(array('name' => $company_name));

                    if (is_null($company)) {
                        continue;
                    }

                    $student_company_match = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\StudentCompanyMatch')
                        ->findOneBy(
                            array(
                                'academic' => $academic,
                                'company' => $company,
                                'year' => $this->getCurrentAcademicYear(),
                            )
                        );

                    if (!is_null($student_company_match)) {
                        continue;
                    }

                    assert($academic instanceof Academic);
                    assert($company instanceof Company);
                    $student_company_match = new StudentCompanyMatch($company, $academic, $this->getCurrentAcademicYear(), $categories);
                    $this->getEntityManager()->persist($student_company_match);
                    $this->getEntityManager()->flush();

                    $count += 1;
                }

                $this->flashMessenger()->success(
                    'Succes',
                    $count . ' matches added'
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

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $matches = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($matches as $m) {
            assert($m instanceof StudentCompanyMatch);
            $item = (object) array();
            $item->id = $m->getId();
            $item->student = $m->getAcademic()->getFullName();
            $item->company = $m->getCompany()->getName();
            $item->categories = $m->getCategoriesAsString();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
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

    /**
     * @return Query|null
     */
    private function search(): ?Query
    {
        switch ($this->getParam('field')) {
            case 'student':
                return $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\StudentCompanyMatch')
                    ->findAllByStudentNameAndYearQuery($this->getParam('string'), $this->getAcademicYear());
            case 'company':
                return $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\StudentCompanyMatch')
                    ->findAllByCompanyNameAndYearQuery($this->getParam('string'), $this->getAcademicYear());
        }
        return null;
    }
}
