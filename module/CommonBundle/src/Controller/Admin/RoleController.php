<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Admin\Role\Add as AddForm,
    CommonBundle\Form\Admin\Role\Edit as EditForm,
    CommonBundle\Entity\Acl\Action as AclAction,
    CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\Acl\Resource,
    Zend\View\Model\ViewModel;

/**
 * RoleController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RoleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CommonBundle\Entity\Acl\Role',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC'
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        $roleCreated = false;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $parents = array();
                if (isset($formData['parents'])) {
                    foreach ($formData['parents'] as $parent) {
                        $parents[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($parent);
                    }
                }

                $actions = array();
                if (isset($formData['actions'])) {
                    foreach ($formData['actions'] as $action) {
                        $actions[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Action')
                            ->findOneById($action);
                    }
                }

                $role = new Role(
                    $formData['name'], false, $parents, $actions
                );

                $this->getEntityManager()->persist($role);

                $this->getEntityManager()->flush();

                $this->_updateCache();

                $form = new AddForm(
                    $this->getEntityManager()
                );

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The role was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_role',
                    array(
                        'action' => 'add'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'roleCreated' => $roleCreated,
            )
        );
    }

    public function editAction()
    {
        if (!($role = $this->_getRole()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $role);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $parents = array();
                if (isset($formData['parents'])) {
                    foreach ($formData['parents'] as $parent) {
                        $parents[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($parent);
                    }
                }
                $role->setParents($parents);

                $actions = array();
                if (isset($formData['actions'])) {
                    foreach ($formData['actions'] as $action) {
                        $actions[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Action')
                            ->findOneById($action);
                    }
                }
                $role->setActions($actions);

                $this->getEntityManager()->flush();

                $this->_updateCache();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The role was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_role',
                    array(
                        'action' => 'manage'
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

        if (!($role = $this->_getRole()))
            return new ViewModel();

        $users = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Person')
            ->findAllByRole($role->getName());

        foreach ($users as $user) {
            $user->removeRole($role);
        }
        $this->getEntityManager()->remove($role);

        $this->getEntityManager()->flush();

        $this->_updateCache();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getRole()
    {
        if (null === $this->getParam('name')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No name was given to identify the role!'
                )
            );

            $this->redirect()->toRoute(
                'admin_role',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $role = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findOneByName($this->getParam('name'));

        if (null === $role) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No role with the given name was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_role',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $role;
    }

    private function _updateCache()
    {
        if (null !== $this->getCache() && $this->getCache()->hasItem('CommonBundle_Component_Acl_Acl')) {
            $this->getCache()->replaceItem(
                'CommonBundle_Component_Acl_Acl',
                new Acl(
                    $this->getEntityManager()
                )
            );
        }
    }
}
