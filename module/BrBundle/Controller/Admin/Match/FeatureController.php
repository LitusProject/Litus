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
use BrBundle\Entity\Product;
use CommonBundle\Component\Document\Generator\Csv as CsvGenerator;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use Laminas\Http\Headers;
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
        $features = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Feature')
            ->findAll();

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
