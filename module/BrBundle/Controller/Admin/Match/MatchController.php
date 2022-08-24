<?php

namespace BrBundle\Controller\Admin\Match;

use BrBundle\Entity\Company;
use BrBundle\Entity\Match;
use BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap;
use BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\ORMException;
use Laminas\Mail\Message;
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

        usort(
            $matches,
            function ($a, $b) {
            // Order the matches by match rating
                return -$a->getMatchPercentage() + $b->getMatchPercentage();
            }
        );

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
                'match'                  => $match,
                'student'                => $student,
                'company'                => $company,
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

    public function sendMailStudentsAction()
    {
        $this->initAjax();

        $maps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap')
            ->findAll();

        $students = array();
        $allStudents = array();
        foreach ($maps as $studentMap) {
            if (!in_array($studentMap->getStudent()->getId(), $students)) {
                array_push($students, $studentMap->getStudent()->getId());
                array_push($allStudents, $studentMap->getStudent());
            }
        }

        $this->sendMailToStudentsAction($allStudents);
        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function sendMailCompaniesAction()
    {
        $this->initAjax();

        $maps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap')
            ->findAll();

        $companies = array();
        $allCompanies = array();
        foreach ($maps as $companyMap) {
            if (!in_array($companyMap->getCompany()->getId(), $companies)) {
                $companies[] = $companyMap->getCompany()->getId();
                $allCompanies[] = $companyMap->getCompany();
            }
        }
        $this->sendMailToCompaniesAction($allCompanies);
        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @param Person  $student
     * @param Company $company
     * @return Match|null
     * @throws ORMException
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

    public function sendMailToCompaniesAction(array $companies)
    {
        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_mail_name');

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.match_wave_companies_body')
        );

        $message = $mailData['content'];
        $subject = $mailData['subject'];

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($message)
            ->setFrom($mailAddress, $mailName)
            ->setSubject($subject)
            ->addTo($mailAddress, $mailName);

        $noEmails = array();
        $bccs = array();
        foreach ($companies as $company) {
            if (is_null($company->getMatchingSoftwareEmail())) {
                $noEmails[] = $company->getName();
            } else {
                $bccs[] = array($company->getMatchingSoftwareEmail(), $company->getName());
            }
        }

        if (count($noEmails) > 0) {
            $body = "The following companies do not have a default email set:\n";
            foreach ($noEmails as $name) {
                $body .= $name."\n";
            }
            $mail->setBody($body);
            if (getenv('APPLICATION_ENV') != 'development') {
                $this->getMailTransport()->send($mail);
            }
        } else {
            foreach ($bccs as $bcc) {
                $mail->addBcc($bcc[0], $bcc[1]);
            }

            if (getenv('APPLICATION_ENV') != 'development') {
                $this->getMailTransport()->send($mail);
            }
        }
    }

    public function sendMailToStudentsAction(array $students)
    {
        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_mail_name');

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.match_wave_students_body')
        );

        $message = $mailData['content'];
        $subject = $mailData['subject'];

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($message)
            ->setFrom($mailAddress, $mailName)
            ->setSubject($subject)
            ->addTo($mailAddress, $mailName);

        foreach ($students as $student) {
            $bcc = $student->getEmail();
            $bccName = $student->getFullName();
            $mail->addBcc($bcc, $bccName);
        }
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}
