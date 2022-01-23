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

namespace BrBundle\Controller\Career;

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

        $sp = True;
        $cp = True;
        foreach ($profiles as $p){
            if ($p->getProfile()->getProfileType() == 'student')
                $sp = false;
            if ($p->getProfile()->getProfileType() == 'company')
                $cp = false;
        }

        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match')
            ->findByStudent($person);

        $bannerText = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_career_banner_text');

        return new ViewModel(
            array(
                'allWaves' => $allWaves,
                'matches' => $matches,
                'lastUpdate' => new \DateTime(), // TODO!!
                'needs_sp'  => $sp,
                'needs_cp'  => $cp,
                'bannerText' => $bannerText,
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

        $sp = True;
        $cp = True;
        foreach ($profiles as $p){
            if ($p->getProfile()->getProfileType() == 'student')
                $sp = false;
            if ($p->getProfile()->getProfileType() == 'company')
                $cp = false;
        }

        $bannerText = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.match_career_banner_text');

        return new ViewModel(
            array(
                'allWaves' => $allWaves,
                'matches' => $matches,
//                'lastUpdate' => new \DateTime(), // TODO!!
                'needs_sp'  => $sp,
                'needs_cp'  => $cp,
                'bannerText' => $bannerText,
            )
        );
    }

    public function addProfileAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        if ($this->isMasterStudent($person) == false){
            $this->flashMessenger()->error(
                'Error',
                "You are not a Master's student, and therefore cannot enroll in the matching platform!"
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
        if ($type == 'company'){
            foreach ($profiles as $p){
                if ($p instanceof Match\Profile\CompanyProfile){
                    return new ViewModel();
                }
            }
            $form = $this->getForm('br_career_match_company_add');
        } else {
            foreach ($profiles as $p){
                if ($p instanceof Match\Profile\StudentProfile){
                    return new ViewModel();
                }
            }
            $form = $this->getForm('br_career_match_student_add');
        }

        $sp = True;
        $cp = True;
        foreach ($profiles as $p){
            if ($p->getProfile()->getProfileType() == 'student')
                $sp = false;
            if ($p->getProfile()->getProfileType() == 'company')
                $cp = false;
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

                // REDIRECT TO OTHER FORM
                if ($type == 'company' && $sp){
                    $this->redirect()->toRoute(
                        'br_career_match',
                        array(
                            'action' => 'addProfile',
                            'type'   => 'student'
                        )
                    );
                } elseif ($type == 'student' && $cp){
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
                'form' => $form,
                'type' => $type,
                'gdpr_text' => unserialize(
                    $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.match_career_profile_GDPR_text'))[$this->getLanguage()->getAbbrev()],
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

        $form = Null;
        // Get the correct form by profile type and check whether there already exists one of this type!
        if ($type == 'company'){
            foreach ($profiles as $p){
                if ($p->getProfile() instanceof Match\Profile\CompanyProfile){
                    $profile = $p->getProfile();
                    $form = $this->getForm('br_career_match_company_edit', array('profile' => $profile));
                }
            }
        } else {
            foreach ($profiles as $p){
                if ($p->getProfile() instanceof Match\Profile\StudentProfile){
                    $profile = $p->getProfile();
                    $form = $this->getForm('br_career_match_student_edit', array('profile' => $profile));
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
                // Remove current Features
                $currentFeatures = $profile->getFeatures();
                foreach ($currentFeatures as $map){
                    $profile->getFeatures()->removeElement($map);
                    $this->getEntityManager()->remove($map);
                }
                $this->getEntityManager()->flush();


                // NEW FEATURES

                // Get new features and importances
                $features = array();
                foreach ($formData as $key => $val){
                    if (str_contains($key, 'feature_')){
                        $id = substr($key, strlen('feature_'));
                        $feature = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')
                            ->findOneById($id);
                        $features[] = array($feature, $val);
                    }
                }

                foreach ($features as $featureAndVal){
                    $map = new ProfileFeatureMap(
                        $this->getEntityManager()
                            ->getRepository('BrBundle\Entity\Match\Feature')
                            ->findOneById($featureAndVal[0]),
                        $profile, $featureAndVal[1]);
                    $this->getEntityManager()->persist($map);
                    $profile->addFeature($map);
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
                'form' => $form,
                'type' => $type,
                'gdpr_text' => unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('br.match_career_profile_GDPR_text'))[$this->getLanguage()->getAbbrev()],
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

    private function isMasterStudent($person){
        // Check whether this person is a Master's student.
        $studies = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($person, $this->getCurrentAcademicYear());

        $masterGroupNames = unserialize($this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.master_group_names'));

        foreach ($masterGroupNames as $groupName){
            $group = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Repository\Group')
                ->findOneByName($groupName);
            $studyMaps = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
                ->findAllByGroup($group);

            foreach($studyMaps as $map){
                foreach($studies as $study){
                    if ($study == $map->getStudy()){
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
