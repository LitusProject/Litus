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

namespace CudiBundle\Controller\Admin\Supplier;

use CudiBundle\Entity\Supplier;
use CudiBundle\Entity\User\Person\Supplier as SupplierPerson;
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
        $supplier = $this->getSupplierEntity();
        if ($supplier === null) {
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
                'supplier'          => $supplier,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $supplier = $this->getSupplierEntity();
        if ($supplier === null) {
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
                        'id'     => $supplier->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'supplier' => $supplier,
                'form'     => $form,
            )
        );
    }

    public function editAction()
    {
        $user = $this->getSupplierPersonEntity();
        if ($user === null) {
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
                        'id'     => $user->getSupplier()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'supplier' => $user->getSupplier(),
                'form'     => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $user = $this->getSupplierPersonEntity();
        if ($user === null) {
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
     * @return Supplier|null
     */
    private function getSupplierEntity()
    {
        $supplier = $this->getEntityById('CudiBundle\Entity\Supplier');

        if (!($supplier instanceof Supplier)) {
            $this->flashMessenger()->error(
                'Error',
                'No supplier was found!'
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
     * @return SupplierPerson|null
     */
    private function getSupplierPersonEntity()
    {
        $person = $this->getEntityById('CudiBundle\Entity\User\Person\Supplier');

        if (!($person instanceof SupplierPerson)) {
            $this->flashMessenger()->error(
                'Error',
                'No person was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_supplier',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $person;
    }
}
