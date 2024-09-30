<?php

namespace LogisticsBundle\Controller;

use CommonBundle\Entity\General\Config;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\FlesserkeArticle;
use LogisticsBundle\Entity\FlesserkeCategory;

/**
 * FlesserkeArticleController
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */

class FlesserkeArticleController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $allArticles = $this->getEntityManager()
            ->getRepository(FlesserkeArticle::class)
            ->findAll();
        $categories = $this->getEntityManager()
            ->getRepository(FlesserkeCategory::class)
            ->findAll();

        $searchForm = $this->getForm('logistics_catalog_flesserke-article_search');

        return new ViewModel(
            array(
                'categories'    => $categories,
                'articles'      => $allArticles,
                'searchForm'    => $searchForm,
            )
        );
    }

    public function addAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'logistics_flesserke_article',
                array(
                    'action' => 'index',
                )
            );
            return new ViewModel();
        }

        $form = $this->getForm(
            'logistics_catalog_flesserke-article_add',
            array(
                'academic' => $academic,
                'academicYear' => $this->getCurrentAcademicYear(true),
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $article = $form->hydrateObject(
                    new FlesserkeArticle()
                );

                $this->getEntityManager()->persist($article);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_flesserke_article',
                    array(
                        'action' => 'index',
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
            $this->redirect()->toRoute(
                'logistics_flesserke_article',
                array(
                    'action' => 'index',
                )
            );
            return new ViewModel();
        }

        $article = $this->getFlesserkeArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'logistics_catalog_flesserke-article_edit',
            array(
                'academic' => $academic,
                'academicYear' => $this->getCurrentAcademicYear(true),
                'article' => $article,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $form->hydrateObject(
                    $article
                );

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_flesserke_article',
                    array(
                        'action' => 'index',
                    )
                );
                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
            ),
        );
    }
    public function deleteAction()
    {
        error_log("Delete action initiated");
        $this->initAjax();

        $article = $this->getFlesserkeArticleEntity();
        if ($article === null) {
            error_log("Article not found");
            return new ViewModel();
        }

        $this->getEntityManager()->remove($article);
        $this->getEntityManager()->flush();
        error_log("Removed");

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function searchAction(): ViewModel
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'logistics_flesserke_article',
                array(
                    'action' => 'index',
                )
            );
            return new ViewModel();
        }

        $numResults = $this->getEntityManager()
            ->getRepository(Config::class)
            ->getConfigValue('search_max_results');

        $articles = $this->getEntityManager()
            ->getRepository(FlesserkeArticle::class)
            ->findAllByNameQuery($this->getParam('string'))
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {
            assert($article instanceof FlesserkeArticle);
            $item = (object)array();
            $item->id = $article->getId();
            $item->barcode = $article->getBarcode();
            $item->name = $article->getName();
            $item->category = $article->getCategory();
            $item->brand = $article->getBrand();
            $item->amount = $article->getAmount();
            $item->unit = $article->getUnit();
            $item->perUnit = $article->getPerUnit();
            $item->expirationDate = $article->getExpirationDate()->format('d/m/Y H:i');
            $item->internalComment = $article->getInternalComment();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }
}
