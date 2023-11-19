<?php

namespace LogisticsBundle\Controller\Admin;

use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Order;
use LogisticsBundle\Entity\Order\OrderArticleMap;
use LogisticsBundle\Entity\Request;

/**
 * OrderController
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
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
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $reviewingUnit = $academic->getUnit($this->getCurrentAcademicYear());

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $articles = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();

        $mapped = array();
        foreach ($articles as $map) {
            if (!isset($mapped[$map->getArticle()->getId()])) {
                $mapped[$map->getArticle()->getId()] = 0;
            }

            $mapped[$map->getArticle()->getId()] += $map->getAmount();
        }

        $lastOrders = $this->getAllOrdersByRequest($order->getRequest());

        $oldOrder = false;
        if ($order != end($lastOrders)) {                                // Look if order is not the oldest order
            $orderIndex = array_search($order, $lastOrders);
            $oldOrder = $lastOrders[$orderIndex + 1];
        }

        $orderForm = $this->getForm('logistics_admin_order_review', $order);

        $articleForm = $this->getForm(
            'logistics_admin_order_orderArticleMap_review',
            array(
                'articles' => $articles,
            )
        );

        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getPost()->toArray()['submit'] == 'articleReview') {
                $articleForm->setData($this->getRequest()->getPost());
                if ($articleForm->isValid()) {
                    $newOrder = $this->getLastOrderByRequest($order->getRequest());
                    $formData = $articleForm->getData();

                    $total = 0;

                    foreach ($articles as $oldMapping) {
                        $id = $oldMapping->getId();
                        $amount = '0';
                        $comment = '';
                        foreach ($formData as $formKey => $formValue) {
                            $split = explode("-", $formKey);
                            $input = $split[0];
                            $type = $split[1];
                            $mappingId = $split[2];

                            if ($input == 'article' && $mappingId == $id) {
                                if ($type == 'amount') {
                                    $amount = $formValue;
                                } else {
                                    $comment = $formValue;
                                }
                            }
                        }

                        $article = $oldMapping->getArticle();
                        $oldAmount = $oldMapping->getAmount();

                        if ($article->getUnit() === $reviewingUnit) {
                            $booking = new OrderArticleMap($newOrder, $article, $amount, $oldAmount, $comment);
                            if ($oldAmount == $formValue) {
                                $booking->setStatus('goedgekeurd');
                            } else {
                                $booking->setStatus('herzien');
                            }
                            $total += $formValue - $oldAmount;
                        } else {
                            $booking = new OrderArticleMap($newOrder, $article, $oldAmount, $oldAmount);
                        }
                        $this->getEntityManager()->persist($booking);
                    }
                    $this->getEntityManager()->flush();

                    //        $this->sendMailToContact($request);

                    if ($total == 0) {
                        $this->flashMessenger()->warn(
                            'Warning',
                            'You have not reviewed any articles!'
                        );
                    } else {
                        $this->flashMessenger()->success(
                            'Success',
                            'The request was successfully reviewed.'
                        );
                    }
                    $this->redirect()->toRoute(
                        'logistics_admin_request',
                        array(
                            'action' => 'manage',
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'order'         => $order,
                'oldOrder'      => $oldOrder,
                'articles'      => $articles,
                'lastOrders'    => $lastOrders,
                'reviewingUnit' => $reviewingUnit,

                'orderForm'     => $orderForm,
                'articleForm'   => $articleForm,
            )
        );
    }



    public function reviewOrderAction()
    {
        $this->initAjax();
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $orderForm = $this->getForm('logistics_admin_order_review');

        if ($this->getRequest()->isPost()) {
            $orderForm->setData($this->getRequest()->getPost());

            if ($orderForm->isValid()) {
                $newOrder = $orderForm->hydrateObject(
                    $this->recreateOrder(
                        $this->getLastOrderByRequest($order->getRequest()),
                        $academic->getUnit($this->getCurrentAcademicYear())->getName())
                );
                $newOrder->review();

                $this->getEntityManager()->persist($newOrder);
                $this->getEntityManager()->flush();

                return new ViewModel(
                    array(
                        'result' => (object)array('status' => 'success'),
                    )
                );
            }
        }
        return new ViewModel();
    }

    public function approveAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }
        $reviewingUnit = $academic->getUnit($this->getCurrentAcademicYear());

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
                || $academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $newOrder = $this->recreateOrder($order, $academic->getUnit($this->getCurrentAcademicYear())->getName());
        $this->getEntityManager()->persist($newOrder);

        $request = $order->getRequest();


        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();
        foreach ($mappings as $mapping) {
            $article = $mapping->getArticle();
            if ($article->getUnit() === $reviewingUnit) {
                $newMapping = new OrderArticleMap($newOrder, $mapping->getArticle(), $mapping->getAmount(), $mapping->getAmount());
                $newMapping->setStatus('goedgekeurd');
                $this->getEntityManager()->persist($newMapping);
            }
        }

        if ($this->isAllMappingsApproved($newOrder)) {
            $newOrder->approve();
            $request->handled();
            $this->flashMessenger()->success(
                'Success',
                'The request was successfully approved.'
            );
        } else {
            $newOrder->review();
            $this->flashMessenger()->success(
                'Success',
                'The articles were successfully approved.'
            );
        }

        $this->getEntityManager()->flush();

//        $this->sendMailToContact($request);

        $this->redirect()->toRoute(
            'logistics_admin_request',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function rejectAction()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }
        $reviewingUnit = $academic->getUnit($this->getCurrentAcademicYear());

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
                || $academic->getUnit($this->getCurrentAcademicYear()) !== $order->getUnit())
        ) {
            return $this->notFoundAction();
        }

        $newOrder = $this->recreateOrder($order, $academic->getUnit($this->getCurrentAcademicYear())->getName());
        $newOrder->reject();
        $this->getEntityManager()->persist($newOrder);

        $request = $order->getRequest();

        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();
        foreach ($mappings as $mapping) {
            $article = $mapping->getArticle();
            if ($article->getUnit() === $reviewingUnit) {
                $newMapping = new OrderArticleMap($newOrder, $article, $mapping->getAmount(), $mapping->getAmount());
                $newMapping->setStatus('afgewezen');
                $this->getEntityManager()->persist($newMapping);
            }
        }

        if ($this->isAllMappingsRejected($newOrder)) {
            $newOrder->reject();
            $request->handled();
            $this->flashMessenger()->success(
                'Success',
                'The request was successfully rejected.'
            );
        } else {
            $newOrder->review();
            $this->flashMessenger()->success(
                'Success',
                'The articles were successfully rejected.'
            );
        }

        $this->getEntityManager()->flush();

//        $this->sendMailToContact($request);

        $this->redirect()->toRoute(
            'logistics_admin_request',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
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
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            return;
        }

        return $academic;
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
     * @param Request $request
     * @return Order
     */
    private function getLastOrderByRequest($request)                  // Gets the most recent order
    {
        $order = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order')
            ->findAllByRequest($request);
        return current($order);                                       // Gets the first element of an array
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
     * @param Order $order
     * @return Order
     */
    private function recreateOrder(Order $order, string $updator)
    {
        $new = new Order($order->getContact(), $order->getRequest(), $updator);
        $new->setCreator($order->getCreator());
        $new->setLocation($order->getLocation());
        $new->setDescription($order->getDescription());
        $new->setEmail($order->getEmail());
        $new->setStartDate($order->getStartDate());
        $new->setEndDate($order->getEndDate());
        $new->setName($order->getName());
        $new->setUnit($order->getUnit());
        $new->pending();
        # In comment: should be fixed later on when adding van system
        # $new->setNeedsRide($order->needsRide());

        return $new;
    }

    /**
     * @param Order $order
     * @return boolean
     */
    private function isAllMappingsApproved($order)
    {
        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();
        foreach ($mappings as $mapping) {
            if (!$mapping->isApproved()) {
                return False;
            }
        }
        return True;
    }

    /**
     * @param Order $order
     * @return boolean
     */
    private function isAllMappingsRejected($order)
    {
        $mappings = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Order\OrderArticleMap')
            ->findAllByOrderQuery($order)->getResult();
        foreach ($mappings as $mapping) {
            if (!$mapping->isRejected()) {
                return False;
            }
        }
        return True;
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
