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

namespace BrBundle\Controller\Corporate;

use BrBundle\Entity\Company;
use BrBundle\Entity\Match;
use BrBundle\Entity\Match\Feature;
use BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap;
use BrBundle\Entity\Match\MatcheeMap\StudentMatcheeMap;
use BrBundle\Entity\Match\Profile\ProfileCompanyMap;
use BrBundle\Entity\Match\Profile\ProfileFeatureMap;
use BrBundle\Entity\Match\Profile\ProfileStudentMap;
use BrBundle\Entity\Match\Wave;
use BrBundle\Entity\Product;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Form\Admin\Element\DateTime;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use CommonBundle\Entity\User\Person;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use function Functional\map;

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

        $allWaves = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Wave')
            ->findAll();

        $wave = $this->getWaveEntity();
        if ($wave === null) {
            $this->redirect()->toRoute(
                'br_corporate_match',
                array(
                    'action' => 'overview',
                    'wave'  => $allWaves[0]->getId(),
                )
            );
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

        $sp = True;
        $cp = True;
        foreach ($profiles as $p){
            if ($p->getProfile()->getProfileType() == 'student')
                $sp = false;
            if ($p->getProfile()->getProfileType() == 'company')
                $cp = false;
        }

        $companyWaves = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\CompanyWave')
            ->findByCompany($person->getCompany());

        foreach ($companyWaves as $cw)
            if ($cw->getWave() == $wave)
                $matches = $cw->getMatches();

        $bannerText = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_corporate_banner_text');

        return new ViewModel(
            array(
                'wave' => $wave,
                'allWaves' => $allWaves,
                'matches' => $matches??null,
                'lastUpdate' => new \DateTime(), // TODO!!
                'needs_sp'  => $sp,
                'needs_cp'  => $cp,
                'bannerText' => $bannerText,
            )
        );
    }

    public function addProfileAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
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
        if ($type == 'company'){
            foreach ($profiles as $p){
                if ($p instanceof Match\Profile\CompanyProfile){
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
            foreach ($profiles as $p){
                if ($p instanceof Match\Profile\StudentProfile){
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

                $map = new ProfileCompanyMap($person->getCompany(), $profile);
                $this->getEntityManager()->persist($map);

                foreach (array_values($formData['features_ids']) as $feature){
                    $map = new ProfileFeatureMap(
                        $this->getEntityManager()
                            ->getRepository('BrBundle\Entity\Match\Feature')
                            ->findOneById($feature),
                        $profile);
                    $this->getEntityManager()->persist($map);
                    $profile->addFeature($map);
                }

                $this->getEntityManager()->flush();
                $this->flashMessenger()->success(
                    'Success',
                    'The profile was successfully created!'
                );

                $this->redirect()->toRoute(
                    'br_corporate_match',
                    array(
                        'action' => 'overview',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'type' => $type,
            )
        );
    }


    public function viewProfileAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
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
        if ($type == 'company'){
            foreach ($profiles as $p){
                if ($p->getProfile() instanceof Match\Profile\CompanyProfile){
                    $profile = $p->getProfile();
                }
            }
        } else {
            foreach ($profiles as $p){
                if ($p->getProfile() instanceof Match\Profile\StudentProfile){
                    $profile = $p->getProfile();
                }
            }
        }

        return new ViewModel(
            array(
                'type'      => $type,
                'features'  => $profile->getFeatures()->toArray(),
            )
        );
    }


    public function editProfileAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

        $type = $this->getParam('type');

        if (!in_array($type, array('company', 'student'))) {
            return new ViewModel();
        }

        $form = Null;
        // Get the correct form by profile type and check whether there already exists one of this type!
        if ($type == 'company'){
            foreach ($profiles as $p){
                if ($p->getProfile() instanceof Match\Profile\CompanyProfile){
                    $profile = $p->getProfile();
                    $form = $this->getForm('br_corporate_match_company_edit', array('profile' => $profile));
                }
            }
        } else {
            foreach ($profiles as $p){
                if ($p->getProfile() instanceof Match\Profile\StudentProfile){
                    $profile = $p->getProfile();
                    $form = $this->getForm('br_corporate_match_student_edit', array('profile' => $profile));
                }
            }
        }

        if (is_null($form)){
            return new ViewModel();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                // Current Features
                $currentFeatures = $profile->getFeatures();
                $currentFeatureIds = array();
                $currentFeatureMaps = array();
                foreach ($currentFeatures as $c){
                    $currentFeatureIds[] = $c->getFeature()->getId();
                    $currentFeatureMaps[$c->getFeature()->getId()] = $c->getId();
                }

                // Form Features
                $formFeatureIds = array_values($formData['features_ids']);

                // Features to remove (old features)
                $oldFeatureIds = array_diff($currentFeatureIds, $formFeatureIds);
                foreach ($oldFeatureIds as $feature){
                    $map = $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Match\Profile\ProfileFeatureMap')
                        ->findOneById($currentFeatureMaps[$feature]);
                    $profile->getFeatures()->removeElement($map);
                    $this->getEntityManager()->remove($map);
                }

                // Features to add (new features)
                $newFeatureIds = array_diff($formFeatureIds, $currentFeatureIds);
                foreach ($newFeatureIds as $feature){
                    $map = new ProfileFeatureMap(
                        $this->getEntityManager()
                            ->getRepository('BrBundle\Entity\Match\Feature')
                            ->findOneById($feature),
                        $profile);
                    $this->getEntityManager()->persist($map);
                    $profile->addFeature($map);
                }

                $this->getEntityManager()->flush();
                $this->flashMessenger()->success(
                    'Success',
                    'The profile was successfully created!'
                );

                $this->redirect()->toRoute(
                    'br_corporate_match',
                    array(
                        'action' => 'overview',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'type' => $type,
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
