<?php

namespace CudiBundle\Controller\Admin;

use CudiBundle\Entity\Supplier;
use Laminas\View\Model\ViewModel;

/**
 * SupplierController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SupplierController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Supplier')
                ->findAllQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('cudi_supplier_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The supplier was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_supplier',
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
        $supplier = $this->getSupplierEntity();
        if ($supplier === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_supplier_edit', $supplier);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The supplier was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_supplier',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'     => $form,
                'supplier' => $supplier,
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
}
