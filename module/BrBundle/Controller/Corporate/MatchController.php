<?php

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Match\Profile\CompanyProfile;
use BrBundle\Entity\Match\Profile\ProfileCompanyMap;
use BrBundle\Entity\Match\Profile\ProfileFeatureMap;
use BrBundle\Entity\Match\Profile\StudentProfile;
use BrBundle\Entity\Match\Wave;
use Laminas\View\Model\ViewModel;

/**
 * MatchController
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class MatchController extends \BrBundle\Component\Controller\CorporateController
{
    public function overviewAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        if (!$person->getCompany()->attendsJobfair()) {
            $this->flashMessenger()->error(
                'Error',
                'Your company is not attending this year\'s Jobfair!'
            );
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

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
            ->getConfigValue('br.match_corporate_banner_text');

        $allWaves = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Wave')
            ->findAll();


        if (is_null($allWaves) || is_null($allWaves[0])) {
            return new ViewModel(
                array(
                    'needs_sp'   => $sp,
                    'needs_cp'   => $cp,
                    'bannerText' => $bannerText,
                )
            );
        }

        $wave = $this->getWaveEntity();
        if ($wave === null) {
            $this->redirect()->toRoute(
                'br_corporate_match',
                array(
                    'action' => 'overview',
                    'wave'   => $allWaves[0]->getId(),
                )
            );
            return new ViewModel();
        }

        $matches = null;
        foreach ($wave->getCompanyWaves() as $cw) {
            if ($cw->getCompany() == $person->getCompany()) {
                $matches = $cw->getMatches();
            }
        }

        $matches = !is_null($matches) ? array_map(
            function ($a) {
                return $a->getMatch();
            },
            $matches
        ) : $matches;

        $gradesMapEnabled = false;
        $gradesMap = array();
        $entries = array();
        if (!is_null($matches) && in_array($this->getCurrentAcademicYear(), $person->getCompany()->getCvBookYears())) {
            $gradesMapEnabled = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_grades_map_enabled');

            $gradesMap = unserialize(
                $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.cv_grades_map')
            );
            foreach ($matches as $match) {
                $entry = $match->getStudentCV($this->getEntityManager(), $this->getCurrentAcademicYear());
                if ($entry != false) {
                    $entries[] = array('id' => $match->getId(), 'cv' => $entry);
                }
            }
        }


        return new ViewModel(
            array(
                'allWaves'           => $allWaves,
                'matches'            => $matches ?? null,
                'lastUpdate'         => new \DateTime(), // TODO!!
                'needs_sp'           => $sp,
                'needs_cp'           => $cp,
                'bannerText'         => $bannerText,
                'academicYear'       => $this->getCurrentAcademicYear()->getCode(),
                'academicYearObject' => $this->getCurrentAcademicYear(),
                'entityManager'      => $this->getEntityManager(),
                'gradesMapEnabled'   => $gradesMapEnabled,
                'gradesMap'          => $gradesMap,
                'profilePath'        => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'entries'            => $entries,
            )
        );
    }

    public function addProfileAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        if (!$person->getCompany()->attendsJobfair()) {
            $this->flashMessenger()->error(
                'Error',
                'Your company is not attending this year\'s Jobfair!'
            );
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

        $type = $this->getParam('type');

        if (!in_array($type, array('company', 'student'))) {
            return new ViewModel();
        }

        // Get the correct form by profile type and check whether there already exists one of this type!
        if ($type == 'company') {
            foreach ($profiles as $p) {
                if (!is_null($profiles) && $p instanceof CompanyProfile) {
                    $this->redirect()->toRoute(
                        'br_corporate_match',
                        array(
                            'action' => 'editProfile',
                            'type'   => $type,
                        )
                    );
                    return $this->editProfileAction();
                }
            }
            $form = $this->getForm('br_corporate_match_company_add');
        } else {
            foreach ($profiles as $p) {
                if (!is_null($profiles) && $p instanceof StudentProfile) {
                    $this->redirect()->toRoute(
                        'br_corporate_match',
                        array(
                            'action' => 'editProfile',
                            'type'   => $type,
                        )
                    );
                    return $this->editProfileAction();
                }
            }
            $form = $this->getForm('br_corporate_match_student_add');
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
                $profile = null;
                if ($type == 'company') {
                    $profile = new CompanyProfile();
                } elseif ($type == 'student') {
                    $profile = new StudentProfile();
                }
                $this->getEntityManager()->persist($profile);

                $map = new ProfileCompanyMap($person->getCompany(), $profile);
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
                // REDIRECT TO OTHER FORM
                if ($type == 'company' && $sp == true) {
                    $this->redirect()->toRoute(
                        'br_corporate_match',
                        array(
                            'action' => 'addProfile',
                            'type'   => 'student',
                        )
                    );
                } elseif ($type == 'student' && $cp) {
                    $this->redirect()->toRoute(
                        'br_corporate_match',
                        array(
                            'action' => 'addProfile',
                            'type'   => 'company',
                        )
                    );
                } else {
                    $this->redirect()->toRoute(
                        'br_corporate_match',
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
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        if (!$person->getCompany()->attendsJobfair()) {
            $this->flashMessenger()->error(
                'Error',
                'Your company is not attending this year\'s Jobfair!'
            );
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

        $type = $this->getParam('type');

        if (!in_array($type, array('company', 'student'))) {
            return new ViewModel();
        }

        // Get the correct form by profile type and check whether there already exists one of this type!
        $profile = null;
        if ($type == 'company') {
            foreach ($profiles as $p) {
                if ($p->getProfile() instanceof CompanyProfile) {
                    $profile = $p->getProfile();
                }
            }
        } else {
            foreach ($profiles as $p) {
                if ($p->getProfile() instanceof StudentProfile) {
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
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        if (!$person->getCompany()->attendsJobfair()) {
            $this->flashMessenger()->error(
                'Error',
                'Your company is not attending this year\'s Jobfair!'
            );
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

        $type = $this->getParam('type');

        if (!in_array($type, array('company', 'student'))) {
            return new ViewModel();
        }

        $form = null;
        // Get the correct form by profile type and check whether there already exists one of this type!
        $profile = null;
        if ($type == 'company') {
            foreach ($profiles as $p) {
                if ($p->getProfile() instanceof CompanyProfile) {
                    $profile = $p->getProfile();
                    $form = $this->getForm('br_corporate_match_company_edit', array('profile' => $profile));
                }
            }
        } else {
            foreach ($profiles as $p) {
                if ($p->getProfile() instanceof StudentProfile) {
                    $profile = $p->getProfile();
                    $form = $this->getForm('br_corporate_match_student_edit', array('profile' => $profile));
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

                $this->redirect()->toRoute(
                    'br_corporate_match',
                    array(
                        'action' => 'viewProfile',
                        'type'   => $type,
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

    // statsAction

    public function interestedAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        if (!$person->getCompany()->attendsJobfair()) {
            $this->flashMessenger()->error(
                'Error',
                'Your company is not attending this year\'s Jobfair!'
            );
            return new ViewModel();
        }

        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Connection')
            ->findInterestedByCompany($person->getCompany());

        $allWaves = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Wave')
            ->findAll();

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

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
            ->getConfigValue('br.match_corporate_banner_text');

        $entries = array();

        $gradesMap = array();
        $gradesMapEnabled = false;
        if (in_array($this->getCurrentAcademicYear(), $person->getCompany()->getCvBookYears())) {
            $gradesMapEnabled = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_grades_map_enabled');

            $gradesMap = unserialize(
                $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.cv_grades_map')
            );
            foreach ($matches as $match) {
                $entry = $match->getStudentCV($this->getEntityManager(), $this->getCurrentAcademicYear());
                if ($entry != false) {
                    $entries[] = array('id' => $match->getId(), 'cv' => $entry);
                }
            }
        }


        return new ViewModel(
            array(
                'allWaves'           => $allWaves,
                'matches'            => $matches ?? null,
                'lastUpdate'         => new \DateTime(), // TODO!!
                'needs_sp'           => $sp,
                'needs_cp'           => $cp,
                'bannerText'         => $bannerText,
                'academicYear'       => $this->getCurrentAcademicYear()->getCode(),
                'academicYearObject' => $this->getCurrentAcademicYear(),
                'entityManager'      => $this->getEntityManager(),
                'gradesMapEnabled'   => $gradesMapEnabled,
                'gradesMap'          => $gradesMap,
                'profilePath'        => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
                'entries'            => $entries,
            )
        );
    }

    public function viewCVAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        if (!$person->getCompany()->attendsJobfair()) {
            return new ViewModel();
        }




        return new ViewModel(
            array(
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
                'br_corporate_match',
                array(
                    'action' => 'overview',
                )
            );

            return;
        }

        return $wave;
    }
}
