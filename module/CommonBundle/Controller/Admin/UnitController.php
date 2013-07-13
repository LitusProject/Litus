<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Organization\Unit,
    CommonBundle\Form\Admin\Unit\Add as AddForm,
    CommonBundle\Form\Admin\Unit\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * UnitController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class UnitController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'CommonBundle\Entity\General\Organization\Unit',
            $this->getParam('page'),
            array(
                'active' => true
            ),
            array(
                'name' => 'ASC'
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if (isset($formData['organization'])) {
                    $organization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOneById($formData['unit']);
                } else {
                    $organization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOne();
                }

                $roles = array();
                if (isset($formData['roles'])) {
                    foreach ($formData['roles'] as $role) {
                        $roles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($role);
                    }
                }

                $coordinatorRoles = array();
                if (isset($formData['coordinatorRoles'])) {
                    foreach ($formData['coordinatorRoles'] as $coordinatorRole) {
                        $coordinatorRoles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($coordinatorRole);
                    }
                }

                $unit = new Unit(
                    $formData['name'],
                    $organization,
                    $roles,
                    $coordinatorRoles
                );

                $this->getEntityManager()->persist($unit);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The unit was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'common_admin_unit',
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

    public function editAction()
    {
        if (!($unit = $this->_getUnit()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $unit);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if (isset($formData['organization'])) {
                    $organization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOneById($formData['unit']);
                } else {
                    $organization = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Organization')
                        ->findOne();
                }

                $roles = array();
                if (isset($formData['roles'])) {
                    foreach ($formData['roles'] as $role) {
                        $roles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($role);
                    }
                }

                $coordinatorRoles = array();
                if (isset($formData['coordinatorRoles'])) {
                    foreach ($formData['coordinatorRoles'] as $coordinatorRole) {
                        $coordinatorRoles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($coordinatorRole);
                    }
                }

                $unit->setName($formData['name'])
                    ->setOrganization($organization)
                    ->setRoles($roles)
                    ->setCoordinatorRoles($coordinatorRoles);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The key was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'common_admin_unit',
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

        if (!($unit = $this->_getUnit()))
            return new ViewModel();

        $unit->deactivate();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getUnit()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the unit!'
                )
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $unit = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findOneById($this->getParam('id'));

        if (null === $unit) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No unit with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'common_admin_unit',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $unit;
    }
}
