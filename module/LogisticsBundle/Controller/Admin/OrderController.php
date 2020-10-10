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

use LogisticsBundle\Entity\Order;
use LogisticsBundle\Entity\Order\OrderArticleMap;
use Zend\View\Model\ViewModel;

/**
 * OrderController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
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

    public function removedAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Order')
                ->findAllRemovedOrOldQuery(),
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
                'form'    => $form,
            )
        );
    }

    public function editAction()
    {
        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

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
                'form'    => $form,
                'order'   => $order,
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
                        }
                        //+1 als map bestaat
                    }
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'No articles were selected to add to the order!'
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The order article mapping was successfully added!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_order',
                    array(
                        'action'       => 'articles',
                        'id'           => $order->getId(),
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
                'form'                => $form,
                'order'               => $order,
                'articles'             => $articles,
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
                            $this->getEntityManager()->persist(new OrderArticleMap($order, $article, 1));
                        }
                        else{
                            $map->setAmount($formData['amount']);
                        }
                    }
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'No articles were selected to add to the order!'
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The order-article mapping was successfully added!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_order',
                    array(
                        'action' => 'articles',
                        'id'  => $order->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'orderArticleMap'   => $orderArticleMap,
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
}
