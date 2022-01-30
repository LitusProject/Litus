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

use BrBundle\Entity\Match\Feature;
use BrBundle\Entity\Match\Profile;
use BrBundle\Entity\Match\Profile\ProfileCompanyMap;
use BrBundle\Entity\Match\Profile\ProfileFeatureMap;
use BrBundle\Entity\Match\Profile\ProfileStudentMap;
use BrBundle\Entity\Product;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Laminas\Http\Headers;
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
        if (!is_null($studentMap)){
            $maps = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap')
                ->findByStudent($studentMap->getStudent());
            foreach ($maps as $map)
                $matches[] = $this->getEntityManager()->getRepository('BrBundle\Entity\Match')
                    ->findOneByStudentMatchee($map);
        } elseif (!is_null($companyMap)){
            $maps = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap')
                ->findByCompany($companyMap->getCompany());
            foreach ($maps as $map)
                $matches[] = $this->getEntityManager()->getRepository('BrBundle\Entity\Match')
                    ->findOneByCompanyMatchee($map);
        }

        $paginator = $this->paginator()->createFromArray(
            $matches,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'student'           => $studentMap?$studentMap->getStudent():null,
                'company'           => $companyMap?$companyMap->getCompany():null,
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
                    foreach ($profiles as $prof){
                        if ($formData['profile_type'] == $prof->getProfile()->getProfileType()){
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
                    foreach ($profiles as $prof){
                        if ($formData['profile_type'] == $prof->getProfile()->getProfileType()){
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
                        'id'     => $profile->getId()
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

            if ($form->isValid()){
                // Remove current Features
                $currentFeatures = $profile->getFeatures();
                foreach ($currentFeatures as $map){
                    $profile->getFeatures()->removeElement($map);
                    $this->getEntityManager()->remove($map);
                }
                $this->getEntityManager()->flush();

                // Add new features with their importances
                foreach ($formData as $key => $val){
                    if (str_contains($key, 'feature_') && $val != 0){
                        $id = substr($key, strlen('feature_'));
                        $map = new ProfileFeatureMap(
                            $this->getEntityManager()
                                ->getRepository('BrBundle\Entity\Match\Feature')
                                ->findOneById($id),$profile, $val);
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
                'form'      => $form,
                'profile'   => $profile,
                'em'        => $this->getEntityManager(),
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
            ->getRepository('BrBundle\Entity\Match')
            ->findAllByProfile($profile);

        foreach ($matches as $match)
            $this->getEntityManager()->remove($match);

        $this->getEntityManager()->remove($profile);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
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
