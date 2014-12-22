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

namespace CudiBundle\Controller\Admin\Supplier;

use Zend\View\Model\ViewModel;

/**
 * UserController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class UserController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($supplier = $this->_getSupplier())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromEntity(
            'CudiBundle\Entity\User\Person\Supplier',
            $this->getParam('page'),
            array(
                'canLogin' => 'true',
                'supplier' => $supplier->getId(),
            ),
            array(
                'username' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'supplier' => $supplier,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        if (!($supplier = $this->_getSupplier())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_supplier_user_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $newUser = $form->hydrateObject();
                $newUser->setSupplier($supplier);

                $newUser->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport(),
                    false
                );

                $this->getEntityManager()->persist($newUser);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The supplier user was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_supplier_user',
                    array(
                        'action' => 'manage',
                        'id' => $supplier->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'supplier' => $supplier,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($user = $this->_getUser())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_supplier_user_edit', $user);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The supplier user was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_supplier_user',
                    array(
                        'action' => 'manage',
                        'id' => $user->getSupplier()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'supplier' => $user->getSupplier(),
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($user = $this->_getUser())) {
            return new ViewModel();
        }

        $user->disableLogin();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \CudiBundle\Entity\Supplier|null
     */
    private function _getSupplier()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the supplier!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_supplier',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $supplier = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findOneById($this->getParam('id'));

        if (null === $supplier) {
            $this->flashMessenger()->error(
                'Error',
                'No supplier with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_supplier',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $supplier;
    }

    /**
     * @return \CudiBundle\Entity\User\Person\Supplier|null
     */
    private function _getUser()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the supplier!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_supplier',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $supplier = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\User\Person\Supplier')
            ->findOneById($this->getParam('id'));

        if (null === $supplier) {
            $this->flashMessenger()->error(
                'Error',
                'No supplier with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_supplier',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $supplier;
    }
}
