<?php

namespace LogisticsBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\FlesserkeCategory;
use LogisticsBundle\Entity\InventoryCategory;

/**
 * CategoryController
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */

class CategoryController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageInventoryAction(): ViewModel
    {
        $categories = $this->getEntityManager()
            ->getRepository(InventoryCategory::class)
            ->findAllQuery();


        $paginator = $this->paginator()->createFromQuery(
            $categories,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addInventoryAction(): ViewModel
    {
        $form = $this->getForm('logistics_admin_category_add-inventory');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $category = $form->hydrateObject(
                    new InventoryCategory()
                );

                $this->getEntityManager()->persist($category);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_admin_category',
                    array(
                        'action' => 'manage_inventory',
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

    public function editInventoryAction(): ViewModel
    {
        $category = $this->getInventoryCategoryEntity();
        if ($category === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'logistics_admin_category_edit-inventory',
            array(
                'category'      => $category,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $form->hydrateObject(
                    $category
                );

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_admin_category',
                    array(
                        'action' => 'manage_inventory',
                    )
                );
                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'article' => $category,
            ),
        );
    }

    public function deleteInventoryAction(): ViewModel
    {
        $this->initAjax();

        $category = $this->getInventoryCategoryEntity();
        if ($category === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($category);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function manageFlesserkeAction(): ViewModel
    {
        $categories = $this->getEntityManager()
            ->getRepository(FlesserkeCategory::class)
            ->findAllQuery();

        $paginator = $this->paginator()->createFromQuery(
            $categories,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addFlesserkeAction(): ViewModel
    {
        $form = $this->getForm('logistics_admin_category_add-flesserke');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $category = $form->hydrateObject(
                    new FlesserkeCategory()
                );

                $this->getEntityManager()->persist($category);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_admin_category',
                    array(
                        'action' => 'manage_flesserke',
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

    public function editFlesserkeAction(): ViewModel
    {
        $category = $this->getFlesserkeCategoryEntity();
        if ($category === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'logistics_admin_category_edit-flesserke',
            array(
                'category'      => $category,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $form->hydrateObject(
                    $category
                );

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'logistics_admin_category',
                    array(
                        'action' => 'manage_flesserke',
                    )
                );
                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'article' => $category,
            ),
        );
    }

    public function deleteFlesserkeAction(): ViewModel
    {
        $this->initAjax();

        $category = $this->getFlesserkeCategoryEntity();
        if ($category === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($category);

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
     * @return InventoryCategory|null
     */
    private function getInventoryCategoryEntity(): ?InventoryCategory
    {
        $category = $this->getEntityById(InventoryCategory::class);
        if (!($category instanceof InventoryCategory)) {
            $this->flashMessenger()->error(
                'Error',
                'No category was found!'
            );
            $this->redirect()->toRoute(
                'logistics_admin_category',
                array(
                    'action' => 'manage_inventory',
                )
            );
            return null;
        }

        return $category;
    }

    /**
     * @return FlesserkeCategory|null
     */
    private function getFlesserkeCategoryEntity(): ?FlesserkeCategory
    {
        $category = $this->getEntityById(FlesserkeCategory::class);
        if (!($category instanceof FlesserkeCategory)) {
            $this->flashMessenger()->error(
                'Error',
                'No category was found!'
            );
            $this->redirect()->toRoute(
                'logistics_admin_category',
                array(
                    'action' => 'manage_flesserke',
                )
            );
            return null;
        }

        return $category;
    }
}
