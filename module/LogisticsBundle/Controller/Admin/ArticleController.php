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

use LogisticsBundle\Entity\Article;
use LogisticsBundle\Entity\Lease\Item;
use Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class ArticleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if ($this->getParam('field') !== null) {
            $articles = $this->search();
        }

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Article')
                ->findAllQuery();
        }

        $paginator = $this->paginator()->createFromQuery(
            $articles,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('logistics_article_add');

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
                    'logistics_admin_article',
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
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_article_edit', $article);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The item was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_article',
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

        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

//        $orderRepo = $this->getEntityManager()
//            ->getRepository('LogisticsBundle\Entity\Order');
//
//        if (count($orderRepo->findUnreturnedByArticle($article)) > 0) {
//            return new ViewModel(
//                array(
//                    'result' => array(
//                        'status' => 'unreturned_orders',
//                    ),
//                )
//            );
//        }
//
//        $orders = $orderRepo->findByArticle($article);
//        foreach ($orders as $order) {
//            $this->getEntityManager()->remove($order);
//        }

        $this->getEntityManager()->remove($article);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $articles = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {

            $item = (object) array();
            $item->id = $article->getId();
            $item->name = $article->getName();
            $item->amountOwned = $article->getAmountOwned();
            $item->amountAvailable = $article->getAmountAvailable();
            $item->category = $article->getCategory();
            $item->location = $article->getLocation();
            $item->spot = $article->getSpot();
            $item->status = $article->getStatus();
            $item->visibility = $article->getVisibility();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Article|null
     */
    private function getArticleEntity()
    {
        $article = $this->getEntityById('LogisticsBundle\Entity\Article');

        if (!($article instanceof Article)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findAllByNameQuery($this->getParam('string'));
            case 'location':
                return $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findAllByLocationQuery($this->getParam('string'));
            case 'status':
                return $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findAllByStatusQuery($this->getParam('string'));
            case 'visibility':
                return $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findAllByVisibilityQuery($this->getParam('string'));
        }
        return;
    }
}
