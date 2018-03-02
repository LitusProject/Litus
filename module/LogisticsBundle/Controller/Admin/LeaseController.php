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
namespace LogisticsBundle\Controller\Admin;

use CommonBundle\Component\Controller\ActionController\AdminController,
    LogisticsBundle\Entity\Lease\Item,
    Zend\View\Model\ViewModel;

/**
 * LeaseController
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class LeaseController extends AdminController
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
        if (!($item = $this->getItemEntity())) {
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

        if (!($item = $this->getItemEntity())) {
            return new ViewModel();
        }

        $leaseRepo = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Lease\Lease');

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
