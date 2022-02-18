<?php

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company;
use BrBundle\Entity\Match;
use BrBundle\Entity\Match\Profile\ProfileFeatureMap;
use BrBundle\Entity\Match\Profile\ProfileStudentMap;
use BrBundle\Entity\Match\Wave;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

/**
 * MatchController
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class MatchController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        $allWaves = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Wave')
            ->findAll();

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findByStudent($person);

        $sp = true;
        $cp = true;
        foreach ($profiles as $p) {
            if ($p->getProfile()->getProfileType() == 'student') {
                $sp = false;
            }
            if ($p->getProfile()->getProfileType() == 'company') {
                $cp = false;
            }
        }

        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match')
            ->findByStudent($person);

        $bannerText = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_career_banner_text');

        return new ViewModel(
            array(
                'allWaves'   => $allWaves,
                'matches'    => $matches,
                'lastUpdate' => new \DateTime(), // TODO!!
                'needs_sp'   => $sp,
                'needs_cp'   => $cp,
                'bannerText' => $bannerText,
                'em'         => $this->getEntityManager(),
                'ay'         => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function wavesAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        $allWaves = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Wave')
            ->findAll();

        $wave = $this->getWaveEntity();
        if ($wave === null) {
            $this->redirect()->toRoute(
                'br_career_match',
                array(
                    'action' => 'overview',
                )
            );
            return new ViewModel();
        }

        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match')
            ->findByStudentAndWave($person, $wave);

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findByStudent($person);

        $sp = true;
        $cp = true;
        foreach ($profiles as $p) {
            if ($p->getProfile()->getProfileType() == 'student') {
                $sp = false;
            }
            if ($p->getProfile()->getProfileType() == 'company') {
                $cp = false;
            }
        }

        $bannerText = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_career_banner_text');

        return new ViewModel(
            array(
                'allWaves'   => $allWaves,
                'matches'    => $matches,
                //                'lastUpdate' => new \DateTime(), // TODO!!
                'needs_sp'   => $sp,
                'needs_cp'   => $cp,
                'bannerText' => $bannerText,
                'em'         => $this->getEntityManager(),
                'ay'         => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function addProfileAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        if ($this->isMasterStudent($person) == false) {
            $this->flashMessenger()->error(
                'Error',
                "You are not a Master's student, and therefore cannot enroll in the matching platform!\n". "Accepted were: "
            );
            $this->redirect()->toRoute(
                'br_career_match',
                array(
                    'action' => 'overview',
                )
            );
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findByStudent($person);

        $type = $this->getParam('type');

        if (!in_array($type, array('company', 'student'))) {
            return new ViewModel();
        }

        // Get the correct form by profile type and check whether there already exists one of this type!
        if ($type == 'company') {
            foreach ($profiles as $p) {
                if ($p instanceof Match\Profile\CompanyProfile) {
                    return new ViewModel();
                }
            }
            $form = $this->getForm('br_career_match_company_add');
        } else {
            foreach ($profiles as $p) {
                if ($p instanceof Match\Profile\StudentProfile) {
                    return new ViewModel();
                }
            }
            $form = $this->getForm('br_career_match_student_add');
        }

        $sp = true;
        $cp = true;
        foreach ($profiles as $p) {
            if ($p->getProfile()->getProfileType() == 'student') {
                $sp = false;
            }
            if ($p->getProfile()->getProfileType() == 'company') {
                $cp = false;
            }
        }


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                if ($type == 'company') {
                    $profile = new Match\Profile\CompanyProfile();
                } elseif ($type == 'student') {
                    $profile = new Match\Profile\StudentProfile();
                }
                $this->getEntityManager()->persist($profile);

                $map = new ProfileStudentMap($person, $profile);
                $this->getEntityManager()->persist($map);

                // Add new features with their importances
                foreach ($formData as $key => $val) {
                    if (str_contains($key, 'feature_') && $val != 0) {
                        $id = substr($key, strlen('feature_'));
                        if (str_contains($key, 'sector_feature_')) {
                            $id = substr($key, strlen('sector_feature_'));
                        }
                        $map = new ProfileFeatureMap(
                            $this->getEntityManager()
                                ->getRepository('BrBundle\Entity\Match\Feature')
                                ->findOneById($id), 
                            $profile,
                            $val
                        );
                        $this->getEntityManager()->persist($map);
                        $profile->addFeature($map);
                    }
                }

                $this->getEntityManager()->flush();
                $this->flashMessenger()->success(
                    'Success',
                    'The profile was successfully created!'
                );

                // REDIRECT TO OTHER FORM
                if ($type == 'company' && $sp) {
                    $this->redirect()->toRoute(
                        'br_career_match',
                        array(
                            'action' => 'addProfile',
                            'type'   => 'student'
                        )
                    );
                } elseif ($type == 'student' && $cp) {
                    $this->redirect()->toRoute(
                        'br_career_match',
                        array(
                            'action' => 'addProfile',
                            'type'   => 'company'
                        )
                    );
                } else {
                    $this->redirect()->toRoute(
                        'br_career_match',
                        array(
                            'action' => 'overview',
                        )
                    );
                }

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'          => $form,
                'type'          => $type,
                'gdpr_text'     => unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('br.match_career_profile_GDPR_text')
                )[$this->getLanguage()->getAbbrev()],
                'sector_points' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.match_sector_feature_max_points'),
            )
        );
    }

    public function viewProfileAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findByStudent($person);

        $type = $this->getParam('type');

        if (!in_array($type, array('company', 'student'))) {
            return new ViewModel();
        }

        // Get the correct form by profile type and check whether there already exists one of this type!
        if ($type == 'company') {
            foreach ($profiles as $p) {
                if ($p->getProfile() instanceof Match\Profile\CompanyProfile) {
                    $profile = $p->getProfile();
                }
            }
        } else {
            foreach ($profiles as $p) {
                if ($p->getProfile() instanceof Match\Profile\StudentProfile) {
                    $profile = $p->getProfile();
                }
            }
        }

        return new ViewModel(
            array(
                'type'     => $type,
                'features' => $profile->getFeatures()->toArray(),
            )
        );
    }

    public function editProfileAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findByStudent($person);

        $type = $this->getParam('type');

        if (!in_array($type, array('company', 'student'))) {
            return new ViewModel();
        }

        $form = null;
        // Get the correct form by profile type and check whether there already exists one of this type!
        if ($type == 'company') {
            foreach ($profiles as $p) {
                if ($p->getProfile() instanceof Match\Profile\CompanyProfile) {
                    $profile = $p->getProfile();
                    $form = $this->getForm('br_career_match_company_edit', array('profile' => $profile));
                }
            }
        } else {
            foreach ($profiles as $p) {
                if ($p->getProfile() instanceof Match\Profile\StudentProfile) {
                    $profile = $p->getProfile();
                    $form = $this->getForm('br_career_match_student_edit', array('profile' => $profile));
                }
            }
        }
        if (is_null($form)) {
            return new ViewModel();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                // Remove current Features
                $currentFeatures = $profile->getFeatures();
                foreach ($currentFeatures as $map) {
                    $profile->getFeatures()->removeElement($map);
                    $this->getEntityManager()->remove($map);
                }
                $this->getEntityManager()->flush();

                // Add new features with their importances
                foreach ($formData as $key => $val) {
                    if (str_contains($key, 'feature_') && $val != 0) {
                        $id = substr($key, strlen('feature_'));
                        if (str_contains($key, 'sector_feature_')) {
                            $id = substr($key, strlen('sector_feature_'));
                        }                        $map = new ProfileFeatureMap(
                            $this->getEntityManager()
                                ->getRepository('BrBundle\Entity\Match\Feature')
                                ->findOneById($id), 
                            $profile,
                            $val
                        );
                        $this->getEntityManager()->persist($map);
                        $profile->addFeature($map);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The profile was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'br_career_match',
                    array(
                        'action' => 'overview',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'          => $form,
                'type'          => $type,
                'gdpr_text'     => unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('br.match_career_profile_GDPR_text')
                )[$this->getLanguage()->getAbbrev()],
                'sector_points' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.match_sector_feature_max_points'),
            )
        );
    }

    public function sendDataAction()
    {
        $this->initAjax();

        $match = $this->getMatchEntity();
        if ($match === null) {
            return new ViewModel();
        }

        $firstInterestedMailEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_enable_first_interested_mail');

        if ($firstInterestedMailEnabled) {
            $interested = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Match')
                ->countInterestedByCompany($match->getCompany());

            if ($interested == 0) {
                $this->sendMailToCompanyAction($match->getCompany());
            }
        }

        $match->setInterested(true);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Wave|null
     */
    private function getWaveEntity()
    {
        $wave = $this->getEntityById('BrBundle\Entity\Match\Wave', 'wave');

        if (!($wave instanceof Wave) && $wave !== null) {
            $this->flashMessenger()->error(
                'Error',
                'No wave was found!'
            );

            $this->redirect()->toRoute(
                'br_career_match',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $wave;
    }

    /**
     * @return Match|null
     */
    private function getMatchEntity()
    {
        $match = $this->getEntityById('BrBundle\Entity\Match', 'match');

        if (!($match instanceof Match)) {
            $this->flashMessenger()->error(
                'Error',
                'No match was found!'
            );

            $this->redirect()->toRoute(
                'br_career_match',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $match;
    }

    private function isMasterStudent($person)
    {
        // Check whether this person is a Master's student.
        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($person, $this->getCurrentAcademicYear());

        $masterGroupIds = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('syllabus.master_group_ids')
        );

        foreach ($masterGroupIds as $groupId) {
            $group = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Group')
                ->findOneById($groupId);

            if (!is_null($group)) {
                $studyMaps = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
                    ->findAllByGroupAndAcademicYear($group, $this->getCurrentAcademicYear());

                foreach ($studyMaps as $map) {
                    foreach ($studies as $study) {
                        if ($study == $map->getStudy()) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function sendMailToCompanyAction(Company $company)
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
                ->getConfigValue('br.match_first_interested_mail_body')
        );

        $message = $mailData['content'];
        $subject = $mailData['subject'];

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($message)
            ->setFrom($mailAddress, $mailName)
            ->setSubject($subject);

        if (is_null($company->getMatchingSoftwareEmail())) {
            $body = "The following company does not have a default email set:\n";
            $body .= $company->getName()."\n";
            $mail->setBody($body)->addTo($mailAddress, $mailName);
        } else {
            $mail->addTo($company->getMatchingSoftwareEmail(), $bccName = $company->getName());
        }

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}
