<?php

namespace CudiBundle\Controller\Admin;

use CudiBundle\Entity\Retail;
use Laminas\View\Model\ViewModel;

/**
 * RetailController
 *
 */
class RetailController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if ($this->getParam('field') !== null) {
            $articles = $this->search();
        }

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Retail')
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
        $form = $this->getForm('cudi_retail_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The retail was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_retail',
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
        $retail = $this->getRetailEntity();
        if ($retail === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_retail_edit', array('retail' => $retail));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The retail was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_retail',
                    array(
                        'action' => 'manage',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'retail' => $retail,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $retail = $this->getRetailEntity();
        if ($retail === null) {
            return new ViewModel();
        }

        $associatedDeals = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Deal')
            ->findAllByRetail($retail->getId());

        foreach ($associatedDeals as $deal) {
            $this->getEntityManager()->remove($deal);
        }
        $this->getEntityManager()->remove($retail);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
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
            $item->article = $article->getArticle()->getTitle();
            $item->owner = $article->getOwner()->getFullName();
            $item->ownerMail = $article->getOwner()->getPersonalEmail();
            $item->price = $article->getPrice();
            $item->anonymous = $article->isAnonymous();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function articleTypeaheadAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $this->getAcademicYearEntity(), 0)->getResult();


        $allowedRetailTypes = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.retail_allowed_types')
        );

        $result = array();
        foreach ($articles as $saleArticle) {
            $article = $saleArticle->getMainArticle();
            if (in_array($article->getType(), $allowedRetailTypes)) {
                $item = (object) array();
                $item->id = $article->getId();
                $item->value = $article->getTitle();
                $result[] = $item;
            }
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Retail|null
     */
    private function getRetailEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Retail');

        if (!($article instanceof Retail)) {
            $this->flashMessenger()->error(
                'Error',
                'No retail was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_retail',
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
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Retail')
                    ->findAllByTitleQuery($this->getParam('string'));
            case 'owner':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Retail')
                    ->findAllByOwnerQuery($this->getParam('string'));
        }
        return;
    }
}
