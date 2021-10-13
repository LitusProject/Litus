<?php

namespace NewsBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use NewsBundle\Entity\Node\News;

/**
 * NewsController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class NewsController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'NewsBundle\Entity\Node\News',
            $this->getParam('page'),
            array(),
            array(
                'creationTime' => 'DESC',
            )
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
        $form = $this->getForm('news_news_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $news = $form->hydrateObject();

                $this->getEntityManager()->persist($news);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The news item was successfully added!'
                );

                $this->redirect()->toRoute(
                    'news_admin_news',
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
        $news = $this->getNewsEntity();
        if ($news === null) {
            return new ViewModel();
        }

        $form = $this->getForm('news_news_edit', $news);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The news item was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'news_admin_news',
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

    public function deleteAction()
    {
        $this->initAjax();

        $news = $this->getNewsEntity();
        if ($news === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($news);
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
     * @return News|null
     */
    private function getNewsEntity()
    {
        $news = $this->getEntityById('NewsBundle\Entity\Node\News');

        if (!($news instanceof News)) {
            $this->flashMessenger()->error(
                'Error',
                'No news was found!'
            );

            $this->redirect()->toRoute(
                'news_admin_news',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $news;
    }
}
