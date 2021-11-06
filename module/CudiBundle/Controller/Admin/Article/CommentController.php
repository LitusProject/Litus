<?php

namespace CudiBundle\Controller\Admin\Article;

use CudiBundle\Entity\Article;
use CudiBundle\Entity\Comment\ArticleMap;
use CudiBundle\Entity\Comment\Comment;
use Laminas\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Comment\ArticleMap')
                ->findAllByArticleQuery($article),
            $this->getParam('page')
        );

        $form = $this->getForm('cudi_article_comment_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $comment = $form->hydrateObject(
                    new Comment(
                        $this->getEntityManager(),
                        $this->getAuthentication()->getPersonObject(),
                        $article
                    )
                );

                $this->getEntityManager()->persist($comment);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The comment was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_article_comment',
                    array(
                        'action' => 'manage',
                        'id'     => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'article'           => $article,
                'form'              => $form,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $mapping = $this->getCommentArticleMapEntity();
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

    /**
     * @return Article|null
     */
    private function getArticleEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Article');

        if (!($article instanceof Article)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return ArticleMap|null
     */
    private function getCommentArticleMapEntity()
    {
        $articleMap = $this->getEntityById('CudiBundle\Entity\Comment\ArticleMap');

        if (!($articleMap instanceof ArticleMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No mapping was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $articleMap;
    }
}
