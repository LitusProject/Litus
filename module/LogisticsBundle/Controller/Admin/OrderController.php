<?php

namespace LogisticsBundle\Controller\Admin;

use DateTime;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Order;
use LogisticsBundle\Entity\Order\OrderArticleMap;
use LogisticsBundle\Entity\Request;

/**
 * OrderController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class OrderController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function viewAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        $lastOrders = $this->getAllOrdersByRequest($order->getRequest());

        $oldOrder = false;
        if ($order != end($lastOrders)) {                                // Look if order is not the oldest order
            $orderIndex = array_search($order, $lastOrders);
            $oldOrder = $lastOrders[$orderIndex + 1];
        }

        $orderForm = $this->getForm(
            'logistics_admin_order_review',
            array(
                'order' => $order,
            )
        );

        $articleForm = $this->getForm(
            'logistics_admin_order_orderarticlemap_review',
            array(
                'articles' => $articles,
            )
        );

        return new ViewModel(
            array(
                'order'         => $order,
                'oldOrder'      => $oldOrder,
                'articles'      => $articles,
                'lastOrders'    => $lastOrders,

                'orderForm'     => $orderForm,
                'articleForm'   => $articleForm,
            )
        );
    }

    public function removedAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order')
                ->findAllRejectedRemovedOrOldQuery(),
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
        $form = $this->getForm('logistics_order_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The order was successfully created!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_order',
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
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        $form = $this->getForm('logistics_admin_order_edit', $order);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The order was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_order',
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
                'order'    => $order,
                'articles' => $articles,
            )
        );
    }

    public function articlesAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Article')
            ->findAll();

        $form = $this->getForm('logistics_order_orderArticleMap_add', array('articles' => $articles));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $articleIds = $formData['articles'];

                if ($articleIds) {
                    foreach ($articleIds as $articleId) {
                        $article = $this->getEntityManager()
                            ->getRepository('LogisticsBundle\Entity\Article')
                            ->findOneById($articleId);

                        $map = $this->getEntityManager()
                            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
                            ->findOneByOrderArticle($order, $article);

                        if ($map === null) {
                            $this->getEntityManager()->persist(new Order\OrderArticleMap($order, $article, 1));
                        } else {
                            $map->setAmount($map->getAmount() + 1);
                            $this->getEntityManager()->flush();
                        }
                    }
                    $this->flashMessenger()->success(
                        'Succes',
                        'The order article mapping was successfully added!'
                    );
                } else {
                    $this->flashMessenger()->error(
                        'Warning',
                        'No articles were selected to add to the order!'
                    );
                }

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_admin_order',
                    array(
                        'action' => 'articles',
                        'id'     => $order->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)
            ->getResult();


        return new ViewModel(
            array(
                'form'     => $form,
                'order'    => $order,
                'articles' => $articles,
            )
        );
    }

    public function articleMappingAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $orderArticleMap = $this->getArticleMapEntity();
        if ($orderArticleMap === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_order_orderArticleMap_edit', array('orderArticleMap' => $orderArticleMap));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The order-article mapping was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_order',
                    array(
                        'action' => 'articles',
                        'id'     => $order->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'            => $form,
                'orderArticleMap' => $orderArticleMap,
            )
        );
    }

    public function conflictingAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Article')
            ->findAll();

        $conflicts = array();

        foreach ($articles as $article) {
            $mappings = array();
            $allmaps = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
                ->findAllActiveByArticleQuery($article)->getResult();

            foreach ($allmaps as $map) {
                if ($map->getOrder()->getEndDate() > new DateTime() && !$map->getOrder()->isRemoved()) {
                    array_push($mappings, $map);
                }
            }

            $max = $article->getAmountAvailable();
            $allOverlap = array();

            foreach ($mappings as $map) {
                $overlapping_maps = $this->findOverlapping($mappings, $map);

                foreach ($overlapping_maps as $overlap) {
                    if (!in_array($overlap, $allOverlap)) {
                        array_push($allOverlap, $overlap);
                    }
                }
            }

            $total = 0;
            foreach ($allOverlap as $overlap) {
                $total += $overlap->getAmount();
            }
            if ($total > $max) {
                $conflict = array(
                    'article'  => $article,
                    'mappings' => $allOverlap,
                    'total'    => $total
                );
                array_push($conflicts, $conflict);
            }
        }

        return new ViewModel(
            array(
                'conflicts' => $conflicts,
            )
        );
    }

    public function deleteArticleAction()
    {
        $this->initAjax();

        $mapping = $this->getArticleMapEntity();
        if ($mapping === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($mapping);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function approveArticleAction()
    {
        $this->initAjax();

        $mapNb = $this->getRequest()->getPost()->get('map');

        $mapping = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findOneById($mapNb);

        if ($mapping === null) {
            return new ViewModel();
        }
        $mapping->setStatus('goedgekeurd');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $order->remove();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Order|null
     */
    private function getOrderEntity()
    {
        $order = $this->getEntityById('LogisticsBundle\Entity\Order');

        if (!($order instanceof Order)) {
            $this->flashMessenger()->error(
                'Error',
                'No order was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $order;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getAllOrdersByRequest($request)                  // Gets all orders except oldest (dummy order)
    {
        $orders = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllByRequest($request);
        array_pop($orders);
        return $orders;
    }

    /**
     * @return OrderArticleMap |null
     */
    private function getArticleMapEntity()
    {
        $map = $this->getEntityById('LogisticsBundle\Entity\Order\OrderArticleMap', 'map');

        if (!($map instanceof OrderArticleMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No mapping was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_order',
                array(
                    'action' => 'manage',

                )
            );

            return;
        }

        return $map;
    }

    /**
     * @param array $a1
     * @param array $a2
     * @return array
     */
    private function mergeArraysUnique(array $a1, array $a2)
    {
        foreach ($a2 as $e2) {
            if (!in_array($e2, $a1)) {
                array_push($a1, $e2);
            }
        }
        return $a1;
    }

    /**
     * @param array $a1
     * @param array $a2
     * @return array
     */
    private function getAllArticleNames(array $a1, array $a2)
    {
        $articleNames = array();
        $diff = $this->mergeArraysUnique($a1, $a2);          // Gets the union of old and new articles
        foreach ($diff as $mapping){
            $name = $mapping->getArticle()->getName();
            if (!in_array($name, $articleNames)){
                $articleNames[] = $mapping->getArticle()->getName();
            }
        }
        return $articleNames;
    }

    private function findOverlapping(array $array, OrderArticleMap $mapping)
    {
        $start = $mapping->getOrder()->getStartDate();
        $end = $mapping->getOrder()->getEndDate();
        $overlapping = array();
        foreach ($array as $map) {
            if ($map->getOrder() !== $mapping->getOrder()
                && !($map->getOrder()->getStartDate() > $end || $map->getOrder()->getEndDate() <= $start)
            ) {
                array_push($overlapping, $map);
            }
        }
        return $overlapping;
    }
}
