<?php

namespace PageBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use PageBundle\Entity\CategoryPage;

/**
 * CategoryPageController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class CategoryPageController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {

        $category_pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\CategoryPage')
            ->findAll();
        
        foreach ($category_pages as $key => $page) {
            if (!$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
                unset($category_pages[$key]);
            }
        }

        $paginator = $this->paginator()->createFromArray(
            $category_pages,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('page_category-page_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $category_page = $form->hydrateObject();

                $this->getEntityManager()->persist($category_page);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The categorypage was successfully added!'
                );

                $this->redirect()->toRoute(
                    'page_admin_categorypage',
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
        $page = $this->getCategoryPageEntity();
        if ($page === null) {
            return new ViewModel();
        }

        $form = $this->getForm('page_category-page_edit', array('categorypage' => $page));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if (isset($formData['submit']) && $form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The categorypage was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'page_admin_categorypage',
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
                'categorypage' => $page,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $page = $this->getCategoryPageEntity();
        if ($page === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($page);
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
     * @return CategoryPage|null
     */
    private function getCategoryPageEntity()
    {
        $page = $this->getEntityById('PageBundle\Entity\CategoryPage');

        if (!($page instanceof CategoryPage) || !$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No categorypage was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_categorypage',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $page;
    }
}
