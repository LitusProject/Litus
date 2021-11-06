<?php

namespace FormBundle\Controller\Admin;

use FormBundle\Entity\Node\Form;
use FormBundle\Entity\ViewerMap;
use Laminas\View\Model\ViewModel;

/**
 * ViewerController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ViewerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $formSpecification = $this->getFormEntity();
        if ($formSpecification === null) {
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
            ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
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
        $formSpecification = $this->getFormEntity();
        if ($formSpecification === null) {
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
            ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
            ->findOneByForm($formSpecification);

        if ($group !== null) {
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

                if ($repositoryCheck !== null) {
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

        $viewer = $this->getViewerMapEntity();
        if ($viewer === null) {
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
            ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
            ->findOneByForm($viewer->getForm());

        if ($group !== null) {
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
