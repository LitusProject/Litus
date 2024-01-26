<?php

namespace BrBundle\Controller\Admin\Match;

use BrBundle\Entity\Match\Profile;
use BrBundle\Entity\Match\Profile\ProfileCompanyMap;
use BrBundle\Entity\Match\Profile\ProfileFeatureMap;
use BrBundle\Entity\Match\Profile\ProfileStudentMap;
use Laminas\View\Model\ViewModel;

/**
 * ProfileController
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class ProfileController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile')
            ->findAll();

        $paginator = $this->paginator()->createFromArray(
            $profiles,
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

    public function matchesAction()
    {
        $profile = $this->getProfileEntity();

        $companyMap = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findOneByProfile($profile);
        $studentMap = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findOneByProfile($profile);

        $matches = array();
        if (!is_null($studentMap)) {
            $maps = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap')
                ->findByStudent($studentMap->getStudent());
            foreach ($maps as $map) {
                $matches[] = $this->getEntityManager()->getRepository('BrBundle\Entity\Connection')
                    ->findOneByStudentMatchee($map);
            }
        } elseif (!is_null($companyMap)) {
            $maps = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap')
                ->findByCompany($companyMap->getCompany());
            foreach ($maps as $map) {
                $matches[] = $this->getEntityManager()->getRepository('BrBundle\Entity\Connection')
                    ->findOneByCompanyMatchee($map);
            }
        }

        $matches = array_filter($matches); // Filter nulls

        $paginator = $this->paginator()->createFromArray(
            $matches,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'student'           => $studentMap ? $studentMap->getStudent() : null,
                'company'           => $companyMap ? $companyMap->getCompany() : null,
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('br_match_profile_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                if ($formData['type'] === 'student') {
                    $student = $this->getEntityManager()->getRepository('CommonBundle\Entity\User\Person')
                        ->findOneById($formData['student']['id']);

                    // GET EXISTING PROFILES AND CHECK IF THIS TYPE ALREADY EXISTS
                    $profiles = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
                        ->findByStudent($student);
                    foreach ($profiles as $prof) {
                        if ($formData['profile_type'] == $prof->getProfile()->getProfileType()) {
                            $this->flashMessenger()->error(
                                'Error',
                                'This type of profile already exists for this student!'
                            );
                            $this->redirect()->toRoute(
                                'br_admin_match_profile',
                                array(
                                    'action' => 'add',
                                )
                            );
                            return new ViewModel();
                        }
                    }
                    $profile = $form->hydrateObject();
                    $this->getEntityManager()->persist($profile);
                    $pmap = new ProfileStudentMap($student, $profile);
                } elseif ($formData['type'] === 'company') {
                    $company = $this->getEntityManager()->getRepository('BrBundle\Entity\Company')
                        ->findOneById($formData['company'][0]);
                    // GET EXISTING PROFILES AND CHECK IF THIS TYPE ALREADY EXISTS
                    $profiles = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
                        ->findByCompany($company);
                    foreach ($profiles as $prof) {
                        if ($formData['profile_type'] == $prof->getProfile()->getProfileType()) {
                            $this->flashMessenger()->error(
                                'Error',
                                'This type of profile already exists for this company!'
                            );
                            $this->redirect()->toRoute(
                                'br_admin_match_profile',
                                array(
                                    'action' => 'add',
                                )
                            );
                            return new ViewModel();
                        }
                    }
                    $profile = $form->hydrateObject();
                    $this->getEntityManager()->persist($profile);
                    $pmap = new ProfileCompanyMap($company, $profile);
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'Something went wrong.'
                    );
                    return new ViewModel();
                }

                $this->getEntityManager()->persist($pmap);

//                // Add new features with their importances
//                foreach ($formData as $key => $val){
//                    if (str_contains($key, 'feature_') && $val != 0){
//                        $id = substr($key, strlen('feature_'));
//                        $map = new ProfileFeatureMap(
//                            $this->getEntityManager()
//                                ->getRepository('BrBundle\Entity\Match\Feature')
//                                ->findOneById($id),$profile, $val);
//                        $this->getEntityManager()->persist($map);
//                        $profile->addFeature($map);
//                    }
//                }

                $this->getEntityManager()->flush();
                $this->flashMessenger()->success(
                    'Success',
                    'The profile was successfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_match_profile',
                    array(
                        'action' => 'edit',
                        'id'     => $profile->getId(),
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
        $profile = $this->getProfileEntity();
        if ($profile === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_match_profile_edit', array('features' => $profile->getFeatures()->toArray(), 'profile' => $profile));

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
                    'The profile was succesfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_match_profile',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'          => $form,
                'profile'       => $profile,
                'em'            => $this->getEntityManager(),
                'sector_points' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.match_sector_feature_max_points'),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $profile = $this->getProfileEntity();
        if ($profile === null) {
            return new ViewModel();
        }

        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Connection')
            ->findAllByProfile($profile);

        foreach ($matches as $m) {
            $this->getEntityManager()->remove($m);
        }

        $this->getEntityManager()->remove($profile);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteAllCompanyProfilesAction()
    {
        $allCompanyProfiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\CompanyProfile')
            ->findAll();

        foreach ($allCompanyProfiles as $companyProfile) {
            $matches = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Connection')
                ->findAllByProfile($companyProfile);

            foreach ($matches as $match) {
                $this->getEntityManager()->remove($match);
            }

            $this->getEntityManager()->remove($companyProfile);
        }

        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'br_admin_match_profile',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function deleteAllStudentProfilesAction()
    {
        $allStudentProfiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\StudentProfile')
            ->findAll();

        foreach ($allStudentProfiles as $studentProfile) {
            $matches = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Connection')
                ->findAllByProfile($studentProfile);

            foreach ($matches as $match) {
                $this->getEntityManager()->remove($match);
            }

            $this->getEntityManager()->remove($studentProfile);
        }

        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'br_admin_match_profile',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    /**
     * @return Profile|null
     */
    private function getProfileEntity()
    {
        $profile = $this->getEntityById('BrBundle\Entity\Match\Profile');

        if (!($profile instanceof Profile)) {
            $this->flashMessenger()->error(
                'Error',
                'No profile was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_match_profile',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $profile;
    }
}
