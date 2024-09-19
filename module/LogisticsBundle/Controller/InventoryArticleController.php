<?php

namespace LogisticsBundle\Controller;

use CommonBundle\Entity\General\Config;
use CommonBundle\Entity\User\Person\Academic;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\InventoryArticle;
use LogisticsBundle\Entity\InventoryCategory;
use RuntimeException;

/**
 * InventoryArticleController
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */

class InventoryArticleController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel();
        }

        $allArticles = $this->findAllArticlesByAcademic($academic);
        $categories = $this->getEntityManager()
            ->getRepository(InventoryCategory::class)
            ->findAll();
        $units = $this->getAllActiveUnits($allArticles);

        $searchForm = $this->getForm('logistics_catalog_inventory-article_search');

        return new ViewModel(
            array(
                'units'         => $units,
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
                'logistics_inventory_article',
                array(
                    'action' => 'index',
                )
            );
            return new ViewModel();
        }

        $form = $this->getForm(
            'logistics_catalog_inventory-article_add',
            array(
                'academic'     => $academic,
                'academicYear' => $this->getCurrentAcademicYear(true),
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $article = $form->hydrateObject(
                    new InventoryArticle()
                );

                $this->getEntityManager()->persist($article);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_inventory_article',
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
                'logistics_inventory_article',
                array(
                    'action' => 'index',
                )
            );
            return new ViewModel();
        }

        $article = $this->getInventoryArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'logistics_catalog_inventory-article_edit',
            array(
                'academic'     => $academic,
                'academicYear' => $this->getCurrentAcademicYear(true),
                'article'      => $article,
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
                    'logistics_inventory_article',
                    array(
                        'action' => 'index',
                    )
                );
                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'article' => $article,
            ),
        );
    }

    public function searchAction(): ViewModel
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'logistics_inventory_article',
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
            ->getRepository(InventoryArticle::class)
            ->findAllByNameQuery($this->getParam('string'))
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {
            assert($article instanceof InventoryArticle);
            $item = (object) array();
            $item->id = $article->getId();
            $item->name = $article->getName();
            $item->status = $article->getStatus();
            $item->internalComment = $article->getInternalComment();
            $item->externalComment = $article->getExternalComment();
            $item->amount = $article->getAmount();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function addArticlesAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'logistics_order',
                array(
                    'action' => 'index',
                )
            );
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $allArticles = $order->getInventoryArticles()->toArray();
        $form = $this->getForm(
            'logistics_catalog_inventory-article_add',
            array(
                'allArticles'    => $allArticles,
            )
        );

        $searchForm = $this->getForm('logistics_catalog_catalog_search');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                // $formData = $form->getData();
                $total = 0;

                // TODO: Implement
                // foreach ($formData as $formKey => $formValue) {
                //
                // }

                if ($total === 0) {
                    $this->flashMessenger()->warn(
                        'Warning',
                        'You have not booked any articles!'
                    );
                    $this->redirect()->toRoute(
                        'logistics_catalog',
                        array(
                            'action' => 'inventory',
                            'order' => $order->getId(),
                        )
                    );
                } else {
                    $this->getEntityManager()->flush();
//                    $this->sendAlertMails($order);

                    $this->flashMessenger()->success(
                        'Success',
                        'The articles have been booked!'
                    );
                    $this->redirect()->toRoute(
                        'logistics_catalog',
                        array(
                            'action' => 'view',
                            'order'  => $order->getId(),
                        )
                    );
                }
                return new ViewModel();
            }
        }

        $categories = $this->getEntityManager()
            ->getRepository(InventoryCategory::class)
            ->findAll();

        return new ViewModel(
            array(
                'isPraesidium'  => $academic->isPraesidium($this->getCurrentAcademicYear(true)),
                'form'          => $form,
                'searchForm'    => $searchForm,

                'units'         => $this->getAllActiveUnits($allArticles),
                'categories'    => $categories,
                'articles'      => $allArticles,
            )
        );
    }

    public function editArticlesAction(): ViewModel
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'logistics_order',
                array(
                    'action' => 'index',
                )
            );
            return new ViewModel();
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        // Check if authenticated to modify order articles
        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear(true))
            || !$order->getUnits()->contains($academic->getUnit($this->getCurrentAcademicYear())))
        ) {
            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'overview',
                )
            );
            return new ViewModel();
        }

        $allArticles = $order->getInventoryArticles()->toArray();
        $form = $this->getForm(
            'logistics_catalog_inventory-article_edit',
            array(
                'allArticles'    => $allArticles,
                'order'          => $order,
            )
        );

        $searchForm = $this->getForm('logistics_catalog_catalog_search');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                // $formData = $form->getData();
                $total = 0;

                // TODO: Implement
                // foreach ($formData as $formKey => $formValue) {
                //
                // }

                if ($total === 0) {
                    $this->flashMessenger()->warn(
                        'Warning',
                        'You have not booked any articles!'
                    );
                    $this->redirect()->toRoute(
                        'logistics_catalog',
                        array(
                            'action' => 'inventory',
                            'order' => $order->getId(),
                        )
                    );
                } else {
                    $this->getEntityManager()->flush();
//                    $this->sendAlertMails($order);

                    $this->flashMessenger()->success(
                        'Success',
                        'The articles have been booked!'
                    );
                    $this->redirect()->toRoute(
                        'logistics_catalog',
                        array(
                            'action' => 'view',
                            'order'  => $order->getId(),
                        )
                    );
                }
                return new ViewModel();
            }
        }

        $categories = $this->getEntityManager()
            ->getRepository(InventoryCategory::class)
            ->findAll();

        return new ViewModel(
            array(
                'isPraesidium'  => $academic->isPraesidium($this->getCurrentAcademicYear(true)),
                'form'          => $form,
                'searchForm'    => $searchForm,

                'units'         => $this->getAllActiveUnits($allArticles),
                'categories'    => $categories,
                'articles'      => $allArticles,
            )
        );
    }

    public function searchArticlesAction(): ViewModel
    {
        $this->initAjax();

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute(
                'logistics_order',
                array(
                    'action' => 'index',
                )
            );
        }

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        if ($academic !== $order->getCreator()
            && (!$academic->isPraesidium($this->getCurrentAcademicYear())
            || !$order->getUnits()->contains($academic->getUnit($this->getCurrentAcademicYear())))
        ) {
            return new ViewModel();
        }

        // TODO: Implement

        return new ViewModel();
    }

    /**
     * @param Academic $academic
     * @return array
     */
    private function findAllArticlesByAcademic(Academic $academic): array
    {
        if ($academic->isPraesidium($this->getCurrentAcademicYear(true))) {
            $unit = $academic->getUnit($this->getCurrentAcademicYear(true));
            return array_merge(
                $this->getEntityManager()
                    ->getRepository(InventoryArticle::class)
                    ->findAllByUnit($unit),
                $this->getEntityManager()
                    ->getRepository(InventoryArticle::class)
                    ->findAllByVisibility('Praesidium'),
                $this->getEntityManager()
                    ->getRepository(InventoryArticle::class)
                    ->findAllByVisibility('Greater VTK'),
                $this->getEntityManager()
                    ->getRepository(InventoryArticle::class)
                    ->findAllByVisibility('Members'),
            );
        }

        if ($academic->isInWorkingGroup($this->getCurrentAcademicYear(true))
        ) {
            return array_merge(
                $this->getEntityManager()
                    ->getRepository(InventoryArticle::class)
                    ->findAllByVisibility('Greater VTK'),
                $this->getEntityManager()
                    ->getRepository(InventoryArticle::class)
                    ->findAllByVisibility('Members'),
            );
        }

        return $this->getEntityManager()
            ->getRepository(InventoryArticle::class)
            ->findAllByVisibility('Members');
    }

    /**
     * @param array $articles
     * @return array
     */
    private function getAllActiveUnits(array $articles): array
    {
        if (count($articles) === 0) {
            return array();
        }

        $unitsArray = array();
        foreach ($articles as $article) {
            if ($article->getUnit()) {
                $unitsArray[] = $article->getUnit()->getName();
            }
        }

        if (count($unitsArray) === 0) {
            throw new RuntimeException('There needs to be at least one unit');
        }

        return array_unique($unitsArray);
    }
}
