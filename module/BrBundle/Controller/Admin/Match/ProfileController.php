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
use BrBundle\Entity\Match\Profile\ProfileFeatureMap;
use BrBundle\Entity\Match\Profile\ProfileStudentMap;
use BrBundle\Entity\Match\Profile\ProfileCompanyMap;
use BrBundle\Entity\Product;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Doctrine\Common\Collections\ArrayCollection;
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
        $profileStudents = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findAll();
        $profileCompanies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findAll();
        $profiles = array_merge($profileStudents, $profileCompanies);

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

    public function addAction()
    {
        $features = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Feature')
            ->findAll();

        $form = $this->getForm('br_match_profile_add', array('features' => $features));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $profile = $form->hydrateObject();

                $this->getEntityManager()->persist($profile);

                if ($formData['type'] == 'student'){
                    $user = new ProfileStudentMap(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\User\Person')
                            ->findOneById($formData['student']['id']), $profile);
                } elseif ($formData['type'] == 'company'){
                    $user = new ProfileCompanyMap(
                        $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Company')
                        ->findOneById($formData['company']), $profile);
                }
                $this->getEntityManager()->persist($user);

                $maps = new ArrayCollection();
                foreach ($formData['features'] as $feature){
                    $map = new ProfileFeatureMap($this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Match\Feature')
                        ->findOneById($feature), $profile);
                    $maps->add($map);
                    $this->getEntityManager()->persist($map);
                }
                $profile->setFeatures($maps);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The profile was succesfully created!'
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
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $feature = $this->getFeatureEntity();
        if ($feature === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_match_feature_edit', array('feature' => $feature));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The feature was succesfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_match_feature',
                    array(
                        'action' => 'manage',
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

    public function deleteAction()
    {
        $this->initAjax();

        $feature = $this->getFeatureEntity();
        if ($feature === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($feature);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }
    
    /**
     * @return Feature|null
     */
    private function getFeatureEntity()
    {
        $feature = $this->getEntityById('BrBundle\Entity\Match\Feature');

        if (!($feature instanceof Feature)) {
            $this->flashMessenger()->error(
                'Error',
                'No feature was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_match_feature',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $feature;
    }
}
