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
class MatchController extends \BrBundle\Component\Controller\CorporateController
{
    public function indexAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

        error_log(sizeof($profiles));

        return new ViewModel();
    }

    public function overviewAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $profiles = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findByCompany($person->getCompany());

        $sp = True;
        $cp = True;
        foreach ($profiles as $p){
            if ($p->getProfile()->getType() == 'Student')
                $sp = null;
            if ($p->getProfile()->getType() == 'Company')
                $cp = null;
        }

        $matches = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match')
            ->findByCompany($person->getCompany());

        return new ViewModel(
            array(
                'matches' => $matches,
                'lastUpdate' => new \DateTime(),
                'needs_sp'  => $sp,
                'needs_cp'  => $cp,
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
        $bool = False;
        if ($type == 'company'){
            foreach ($profiles as $p){
                if ($p instanceof Match\Profile\CompanyProfile){
                    return new ViewModel();
                }
            }
            $form = $this->getForm('br_match_profile_company_add_company');
        } else {
            foreach ($profiles as $p){
                if ($p instanceof Match\Profile\StudentProfile){
                    return new ViewModel();
                }
            }
            $form = $this->getForm('br_match_profile_company_add_student');
        }


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $profile = $form->hydrateObject();
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
            )
        );
    }


}
