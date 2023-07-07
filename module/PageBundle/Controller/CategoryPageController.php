<?php

namespace PageBundle\Controller;

use Laminas\View\Model\ViewModel;
use PageBundle\Entity\Category;
use PageBundle\Entity\Node\CategoryPage;

/**
 * PageController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class CategoryPageController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $page = $this->getCategoryPageEntity();
        if ($page === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            array(
                'category_page' => $page,
            )
        );
    }

    /**
     * @return CategoryPage|null
     */
    private function getCategoryPageEntity()
    {
        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findByParent(null);

        $name = $this->getParam('name');
        foreach ($categories as $category) {
            if ($name == $category->getName($this->getLanguage())){
                $page = $this->getEntityManager()
                    ->getRepository('PageBundle\Entity\Node\CategoryPage')
                    ->findByCategory($category);
                break;
            }
        }

        $page = $page[0]; //TODO fix dat geen array is maar direct page
        if (!($page instanceof CategoryPage)) {
            return;
        }


        return $page;
    }
}
