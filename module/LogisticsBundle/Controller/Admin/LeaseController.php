<?php

namespace LogisticsBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Lease\Item;

/**
 * LeaseController
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class LeaseController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'LogisticsBundle\Entity\Lease\Item',
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
        $form = $this->getForm('logistics_lease_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The item was successfully added!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_lease',
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
        $item = $this->getItemEntity();
        if ($item === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_lease_edit', $item);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The item was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_lease',
                    array(
                        'action' => 'manage',
                    )
                );
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

        $item = $this->getItemEntity();
        if ($item === null) {
            return new ViewModel();
        }

        $leaseRepo = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Lease');

        if (count($leaseRepo->findUnreturnedByItem($item)) > 0) {
            return new ViewModel(
                array(
                    'result' => array(
                        'status' => 'unreturned_leases',
                    ),
                )
            );
        }

        $leases = $leaseRepo->findByItem($item);
        foreach ($leases as $lease) {
            $this->getEntityManager()->remove($lease);
        }

        $this->getEntityManager()->remove($item);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Item|null
     */
    private function getItemEntity()
    {
        $item = $this->getEntityById('LogisticsBundle\Entity\Lease\Item');

        if (!($item instanceof Item)) {
            $this->flashMessenger()->error(
                'Error',
                'No item was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_lease',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $item;
    }
}
