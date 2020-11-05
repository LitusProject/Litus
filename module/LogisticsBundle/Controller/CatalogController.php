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

namespace LogisticsBundle\Controller;

use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Article;

/**
 */
class CatalogController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $articleSearchForm = $this->getForm('logistics_catalog_search_article');
        $query = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Article')
            ->findAllQuery(); //TODO: welke query moet hier?

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $articleSearchForm->setData($formData);

            if ($articleSearchForm->isValid()) {
                $formData = $articleSearchForm->getData();

                $repository = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article');

                $type = $formData['type'] == 'all' ? null : $formData['type'];
                $location = $formData['location'] == 'all' ? null : $formData['location'];

//                $query = $repository->findAllActiveByTypeAndLocationQuery($type, $location); //TODO: create
                $query = $repository->findAllQuery(); //TODO: make line above functional
            }
        }
        $paginator = $this->paginator()->createFromQuery(
            $query,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'catalogSearchForm' => $articleSearchForm,
            )
        );
    }

    public function viewAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }


        return new ViewModel(
            array(
                'article'  => $article,
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
                'No Article was found!'
            );

            $this->redirect()->toRoute(
                'logistics_catalog',
                array(
                    'action' => 'index',
                )
            );

            return;
        }

        return $article;
    }
}
