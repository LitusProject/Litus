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
use Laminas\View\Model\ViewModel;

/**
 * FeatureController
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class FeatureController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $features = array_reverse(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Match\Feature')
                ->findAll()
        );

        $paginator = $this->paginator()->createFromArray(
            $features,
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
        $form = $this->getForm('br_match_feature_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $feature = $form->hydrateObject(new Feature());

                $this->getEntityManager()->persist($feature);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The feature was succesfully created!'
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
                $feature = $form->hydrateObject($feature);
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

    public function bonusMalusAction()
    {
        $feature = $this->getFeatureEntity();
        if ($feature === null) {
            return new ViewModel();
        }

        $allFeatures = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')
            ->findAll();

        $form = $this->getForm('br_match_feature_bonusmalus', array('feature' => $feature, 'features' => $allFeatures));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->updateBonusses($feature, $formData);
                $this->updateMalusses($feature, $formData);

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

    private function updateBonusses(Feature $feature, $data)
    {
        if (!isset($data['bonus'])) {
            return null;
        }

        // Get all old bonusses
        $old = $feature->getBonus();
        $oldIds = array();
        foreach ($old as $old_) {
            $oldIds[] = $old_->getId();
        }

        // Get new
        $newIds = array_values($data['bonus']);

        // Add to the database
        foreach ($newIds as $new) {
            if (!in_array($new, $oldIds)) {
                $newBonus = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')
                    ->findOneById($new);
                $feature->addMyBonus($newBonus);
            }
        }

        // Get all old myBonusses (!)
        $myOld = $feature->getMyBonus();
        $myOldIds = array();
        foreach ($myOld as $old_) {
            $myOldIds[] = $old_->getId();
        }

        // Remove old profileFeatureMaps from database
        foreach (array_diff($oldIds, $newIds) as $oldOne) {
            $oldBonus = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')
                ->findOneById($oldOne);

            if (in_array($oldOne, $myOldIds)) {
                $feature->removeMyBonus($oldBonus); // Remove as myBonus
            } else {
                $oldBonus->removeMyBonus($feature); // Remove as theirBonus
            }
        }

        $this->getEntityManager()->flush();

        return;
    }

    private function updateMalusses(Feature $feature, $data)
    {
        if (!isset($data['malus'])) {
            return null;
        }

        // Get all old malusses
        $old = $feature->getMalus();
        $oldIds = array();
        foreach ($old as $old_) {
            $oldIds[] = $old_->getId();
        }

        // Get new
        $newIds = array_values($data['malus']);

        // Add to the database
        foreach ($newIds as $new) {
            if (!in_array($new, $oldIds)) {
                $newMalus = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')
                    ->findOneById($new);
                $feature->addMyMalus($newMalus);
            }
        }

        // Get all old myMalusses (!)
        $myOld = $feature->getMyMalus();
        $myOldIds = array();
        foreach ($myOld as $old_) {
            $myOldIds[] = $old_->getId();
        }

        // Remove old profileFeatureMaps from database
        foreach (array_diff($oldIds, $newIds) as $oldOne) {
            $oldMalus = $this->getEntityManager()->getRepository('BrBundle\Entity\Match\Feature')
                ->findOneById($oldOne);

            if (in_array($oldOne, $myOldIds)) {
                $feature->removeMyMalus($oldMalus); // Remove as myMalus
            } else {
                $oldMalus->removeMyMalus($feature); // Remove as theirMalus
            }
        }

        $this->getEntityManager()->flush();

        return;
    }
}
