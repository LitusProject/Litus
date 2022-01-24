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

namespace BrBundle\Controller\Admin\Match;

use BrBundle\Entity\Company;
use BrBundle\Entity\Match;
use BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap;
use BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap;
use CommonBundle\Entity\User\Person;
use Laminas\View\Model\ViewModel;

/**
 * MatchController
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class MatchController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match')
            ->findAll();

        $paginator = $this->paginator()->createFromArray(
            $matches,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'em'                => $this->getEntityManager(),
            )
        );
    }

    public function viewAction()
    {
        $match = $this->getMatchEntity();
        if ($match === null) {
            return new ViewModel();
        }

        $student = $match->getStudentMatchee()->getStudent();
        $company = $match->getCompanyMatchee()->getCompany();

        $studentStudentFeatures = array();
        $companyStudentFeatures = array();
        // THE STUDENT FEATURES
        foreach ($match->getStudentMatchee()->getStudentProfile()->getFeatures() as $feature) {
            $feat = (object) array();
            $feat->name = $feature->getFeature()->getName();
            $feat->importance = $feature->getImportance();
            $studentStudentFeatures[$feature->getFeature()->getId()] = $feat;
        }
        foreach ($match->getCompanyMatchee()->getStudentProfile()->getFeatures() as $feature) {
            $feat = (object) array();
            $feat->name = $feature->getFeature()->getName();
            $feat->importance = $feature->getImportance();
            $companyStudentFeatures[$feature->getFeature()->getId()] = $feat;
        }
        foreach (array_intersect(array_keys($studentStudentFeatures), array_keys($companyStudentFeatures)) as $shared) {
            $studentStudentFeatures[$shared]->shared = true;
            $companyStudentFeatures[$shared]->shared = true;
        }

        $studentCompanyFeatures = array();
        $companyCompanyFeatures = array();
        // THE COMPANY FEATURES
        foreach ($match->getStudentMatchee()->getCompanyProfile()->getFeatures() as $feature) {
            $feat = (object) array();
            $feat->name = $feature->getFeature()->getName();
            $feat->importance = $feature->getImportance();
            $studentCompanyFeatures[$feature->getFeature()->getId()] = $feat;
        }
        foreach ($match->getCompanyMatchee()->getCompanyProfile()->getFeatures() as $feature) {
            $feat = (object) array();
            $feat->name = $feature->getFeature()->getName();
            $feat->importance = $feature->getImportance();
            $companyCompanyFeatures[$feature->getFeature()->getId()] = $feat;
        }
        foreach (array_intersect(array_keys($studentCompanyFeatures), array_keys($companyCompanyFeatures)) as $shared) {
            $studentCompanyFeatures[$shared]->shared = true;
            $companyCompanyFeatures[$shared]->shared = true;
        }

        return new ViewModel(
            array(
                'match' => $match,
                'student'   => $student,
                'company'   => $company,
                'studentStudentFeatures' => $studentStudentFeatures,
                'companyStudentFeatures' => $companyStudentFeatures,
                'studentCompanyFeatures' => $studentCompanyFeatures,
                'companyCompanyFeatures' => $companyCompanyFeatures,
            )
        );
    }

    public function generateMatchesAction()
    {
        $companyMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findAll();
        $studentMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findAll();

        $companies = array();
        $allCompanies = array();
        $students = array();
        $allStudents = array();
        foreach ($companyMaps as $companyMap) {
            if (!in_array($companyMap->getCompany()->getId(), $companies)) {
                array_push($companies, $companyMap->getCompany()->getId());
                array_push($allCompanies, $companyMap->getCompany());
            }
        }
        foreach ($studentMaps as $studentMap) {
            if (!in_array($studentMap->getStudent()->getId(), $students)) {
                array_push($students, $studentMap->getStudent()->getId());
                array_push($allStudents, $studentMap->getStudent());
            }
        }

        foreach ($allStudents as $student) {
            foreach ($allCompanies as $company) {
                $map = $this->getEntityManager()->getRepository('BrBundle\Entity\Match')
                    ->findOneByStudentAndCompany($student, $company);
                if ($map === null) {
                    $match = $this->generateMatch($student, $company);
                    if ($match !== null) {
                        $this->getEntityManager()->persist($match);
                    }
                }
            }
        }
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'br_admin_match_match',
            array(
                'action' => 'manage',
            )
        );
        return new ViewModel();
    }

    /**
     * @param Person $student
     * @param Company $company
     * @return Match|null
     * @throws \Doctrine\ORM\ORMException
     */
    private function generateMatch(Person $student, Company $company)
    {
        $studentProfiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findByStudent($student);

        $companyProfiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($company);

        foreach ($studentProfiles as $profile) {
            if ($profile->getProfile()->getProfileType() === 'student') {
                $studentStudentProfile = $profile->getProfile();
            } elseif ($profile->getProfile()->getProfileType() === 'company') {
                $studentCompanyProfile = $profile->getProfile();
            }
        }
        foreach ($companyProfiles as $profile) {
            if ($profile->getProfile()->getProfileType() === 'student') {
                $companyStudentProfile = $profile->getProfile();
            } elseif ($profile->getProfile()->getProfileType() === 'company') {
                $companyCompanyProfile = $profile->getProfile();
            }
        }

        if ($studentStudentProfile === null || $studentCompanyProfile === null
            || $companyStudentProfile === null || $companyCompanyProfile === null
        ) {
            return null;
        }

        $companyMatchee = new CompanyMatcheeMap($companyCompanyProfile, $companyStudentProfile, $company);
        $studentMatchee = new StudentMatcheeMap($studentCompanyProfile, $studentStudentProfile, $student);

        $this->getEntityManager()->persist($companyMatchee);
        $this->getEntityManager()->persist($studentMatchee);


        return new Match($studentMatchee, $companyMatchee);
    }

    public function deleteAction()
    {
        $this->initAjax();

        $match = $this->getMatchEntity();
        if ($match === null) {
            return new ViewModel();
        }
        $this->getEntityManager()->remove($match);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->remove($match->getCompanyMatchee());
        $this->getEntityManager()->remove($match->getStudentMatchee());
        $this->getEntityManager()->flush();
        if (!is_null($match->getWave())) {
            $match->getWave()->getWave()->removeMatch($match);
            $this->getEntityManager()->remove($match->getWave());
            $this->getEntityManager()->flush();
        }




        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }
    
    /**
     * @return Match|null
     */
    private function getMatchEntity()
    {
        $match = $this->getEntityById('BrBundle\Entity\Match');

        if (!($match instanceof Match)) {
            $this->flashMessenger()->error(
                'Error',
                'No match was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_match_match',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $match;
    }
}
