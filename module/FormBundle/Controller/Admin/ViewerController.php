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

namespace FormBundle\Controller\Admin;

use FormBundle\Entity\Node\Form,
    FormBundle\Entity\ViewerMap,
    Zend\View\Model\ViewModel;

/**
 * ViewerController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ViewerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($formSpecification = $this->getFormEntity())) {
            return new ViewModel();
        }

        if (!$formSpecification->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to edit this form!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $viewers = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findByForm($formSpecification);

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($formSpecification);

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'viewers'           => $viewers,
                'hasGroup'          => $group !== null,
            )
        );
    }

    public function addAction()
    {
        if (!($formSpecification = $this->getFormEntity())) {
            return new ViewModel();
        }

        if (!$formSpecification->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to edit this form!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($formSpecification);

        if (null !== $group) {
            $this->flashMessenger()->error(
                'Error',
                'This form is in a group, you cannot edit the viewer here!'
            );

            $this->redirect()->toRoute(
                'form_admin_form_viewer',
                array(
                    'action' => 'manage',
                    'id'     => $formSpecification->getId(),
                )
            );

            return new ViewModel();
        }

        $form = $this->getForm('form_viewer_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person')
                    ->findOneById($formData['person']['id']);

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\ViewerMap')
                    ->findOneBy(
                        array(
                            'form'   => $formSpecification,
                            'person' => $person,
                        )
                    );

                if (null !== $repositoryCheck) {
                    $this->flashMessenger()->error(
                        'Error',
                        'This user has already been given access to this list!'
                    );
                } else {
                    $this->getEntityManager()->persist(
                        $form->hydrateObject(new ViewerMap($formSpecification))
                    );

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The viewer was successfully created!'
                    );
                }

                $this->redirect()->toRoute(
                    'form_admin_form_viewer',
                    array(
                        'action' => 'manage',
                        'id'     => $formSpecification->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'formSpecification' => $formSpecification,
                'form'              => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($viewer = $this->getViewerMapEntity())) {
            return new ViewModel();
        }

        if (!$viewer->getForm()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authorized to edit this form!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($viewer->getForm());

        if (null !== $group) {
            $this->flashMessenger()->error(
                'Error',
                'This form is in a group, you cannot edit the viewer here!'
            );

            $this->redirect()->toRoute(
                'form_admin_form_viewer',
                array(
                    'action' => 'manage',
                    'id'     => $viewer->getForm()->getId(),
                )
            );

            return new ViewModel();
        }

        $this->getEntityManager()->remove($viewer);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Form|null
     */
    private function getFormEntity()
    {
        $form = $this->getEntityById('FormBundle\Entity\Node\Form');

        if (!($form instanceof Form)) {
            $this->flashMessenger()->error(
                'Error',
                'No form was found!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $form;
    }

    /**
     * @return ViewerMap|null
     */
    private function getViewerMapEntity()
    {
        $viewer = $this->getEntityById('FormBundle\Entity\ViewerMap');

        if (!($viewer instanceof ViewerMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No viewer was found!'
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $viewer;
    }
}
