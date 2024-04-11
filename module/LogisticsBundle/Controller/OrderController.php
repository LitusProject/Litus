<?php

namespace LogisticsBundle\Controller;

use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Order;

/**
 * OrderController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class OrderController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $orders = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllByCreator($academic);

        $unit = $academic->getUnit($this->getCurrentAcademicYear(true));

        if ($unit) {
            // TODO: check if this works
            $unitOrders = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order')
                ->findAllByUnit($unit);
            $orders = $this->mergeArraysUnique($orders, $unitOrders);
        }

        return new ViewModel(
            array(
                'orders' => $orders,
                'fathom' => $this->getFathomInfo(),
            )
        );
    }

    public function viewAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        if ($academic !== $order->getCreator()
            &&(!$academic->isPraesidium($this->getCurrentAcademicYear())
            || !$order->getUnits()->contains($academic->getUnit($this->getCurrentAcademicYear())))
        ) {
            return $this->notFoundAction();
        }

        $articles = $order->getAllArticles()->toArray();
        $history = $order->getHistory()->getOrders();

        return new ViewModel(
            array(
                'order'      => $order,
                'articles'   => $articles,
                'history'    => $history,
            )
        );
    }

    public function addAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'logistics_catalog_order_add',
            array(
                'academic'     => $academic,
                'academicYear' => $this->getCurrentAcademicYear(true),
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                // TODO: Fix that this form works
                $order = $form->hydrateObject(
                    new Order($academic)
                );

                $this->getEntityManager()->persist($order);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'inventory',
                        'order'  => $order->getId(),
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

    public function editAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
            || !$order->getUnits()->contains($academic->getUnit($this->getCurrentAcademicYear())))
        ) {
            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );
            return $this->notFoundAction();
        }

        $form = $this->getForm(
            'logistics_catalog_order_edit',
            array(
                'academic'      => $academic,
                'academicYear'  => $this->getCurrentAcademicYear(true),
                'order'         => $order,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                // TODO: Implement
                $newOrder = clone $order;

                $this->flashMessenger()->success(
                    'Success',
                    'The order has been modified and sent for approval.'
                );

                $this->redirect()->toRoute(
                    'logistics_catalog',
                    array(
                        'action' => 'view', 'order' => $newOrder->getId(),
                    )
                );
                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'  => $form,
                'order' => $order,
            )
        );
    }

    public function cancelAction(): ViewModel
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return $this->notFoundAction();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
            || !$order->getUnits()->contains($academic->getUnit($this->getCurrentAcademicYear())))
        ) {
            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );
            return $this->notFoundAction();
        }

        $order->setStatus('canceled');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function removeAction(): ViewModel
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return $this->notFoundAction();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
            || !$order->getUnits()->contains($academic->getUnit($this->getCurrentAcademicYear())))
        ) {
            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );
            return $this->notFoundAction();
        }

        // TODO: Implement

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @param array $a1
     * @param array $a2
     * @return array
     */
    private function mergeArraysUnique(array $a1, array $a2): array
    {
        foreach ($a2 as $e2) {
            if (!in_array($e2, $a1)) {
                $a1[] = $e2;
            }
        }
        return $a1;
    }
}
