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
    public function indexAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findByStudent($person);

        error_log(sizeof($profiles));

        return new ViewModel();
    }

    public function overviewAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

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

        return new ViewModel(
            array(
                'matches' => $matches,
                'lastUpdate' => new \DateTime(), // TODO!!
                'needs_sp'  => $sp,
                'needs_cp'  => $cp,
            )
        );
    }

    public function addProfileAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        if ($person === null) {
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person);

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
            )
        );
    }


}
