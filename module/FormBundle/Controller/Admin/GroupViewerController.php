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
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    FormBundle\Form\Admin\Viewer\Add as AddForm,
    FormBundle\Entity\ViewerMap,
    Zend\View\Model\ViewModel;

/**
 * GroupViewerController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class GroupViewerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($group = $this->_getGroup()))
            return new ViewModel();

        if (!$group->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You are not authorized to edit this group!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $viewers = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findByForm($group->getForms()[0]->getForm());

        return new ViewModel(
            array(
                'group' => $group,
                'viewers' => $viewers,
            )
        );
    }

    public function addAction()
    {
        if (!($group = $this->_getGroup()))
            return new ViewModel();

        if (!$group->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You are not authorized to edit this group!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $form = new AddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person');
                if ($formData['person_id'] == '') {
                    $person = $repository->findOneByUsername($formData['person_name']);
                } else {
                    $person = $repository->findOneById($formData['person_id']);
                }

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\ViewerMap')
                    ->findOneBy(
                        array(
                            'form' => $group->getForms()[0]->getForm(),
                            'person' => $person
                        )
                    );

                if (null !== $repositoryCheck) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Success',
                            'This user has already been given access to this list!'
                        )
                    );
                } else {
                    foreach($group->getForms() as $form) {
                        $viewer = new ViewerMap(
                            $form->getForm(),
                            $person,
                            $formData['edit'],
                            $formData['mail']
                        );
                        $this->getEntityManager()->persist($viewer);
                    }

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'The viewer was successfully created!'
                        )
                    );
                }

                $this->redirect()->toRoute(
                    'form_admin_group_viewer',
                    array(
                        'action' => 'manage',
                        'id' => $group->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'group' => $group,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($viewer = $this->_getViewer()))
            return new ViewModel();

        if (!$viewer->getForm()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You are not authorized to edit this group!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($viewer->getForm());

        if (null == $group) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $viewers = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findAllByGroupAndPerson($group->getGroup(), $viewer->getPerson());

        foreach($viewers as $viewer) {
            $this->getEntityManager()->remove($viewer);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getGroup()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the group!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Group')
            ->findOneById($this->getParam('id'));

        if (null === $group) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No group with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        if (sizeof($group->getForms()) == 0) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'This group has no forms!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_group',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $group;
    }

    private function _getViewer()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the viewer!'
                )
            );

            $this->redirect()->toRoute(
                'form_admin_form',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $viewer = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneById($this->getParam('id'));

        if (null === $viewer) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No viewer with the given ID was found!'
                )
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
