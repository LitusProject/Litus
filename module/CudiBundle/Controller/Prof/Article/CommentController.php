<?php

namespace CudiBundle\Controller\Prof\Article;

use CudiBundle\Entity\Article;
use CudiBundle\Entity\Comment\ArticleMap;
use CudiBundle\Entity\Comment\Comment;
use Laminas\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comment\ArticleMap')
            ->findByArticle($article);

        $form = $this->getForm('cudi_prof_comment_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $comment = new Comment(
                    $this->getEntityManager(),
                    $this->getAuthentication()->getPersonObject(),
                    $article,
                    $formData['text'],
                    'external'
                );

                $this->getEntityManager()->persist($comment);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The comment was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_prof_article_comment',
                    array(
                        'action'   => 'manage',
                        'id'       => $article->getId(),
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'article'  => $article,
                'form'     => $form,
                'mappings' => $mappings,
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

        if ($mapping->getComment()->getPerson()->getId() != $this->getAuthentication()->getPersonObject()->getId()) {
            return array(
                'result' => (object) array('status' => 'error'),
            );
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
     * @param  integer|null $id
     * @return Article|null
     */
    private function getArticleEntity($id = null)
    {
        $id = $id ?? $this->getParam('id', 0);

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneByIdAndProf($id, $this->getAuthentication()->getPersonObject());

        if (!($article instanceof Article)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action'   => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
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

        if (!($articleMap instanceof ArticleMap) || $this->getArticleEntity($articleMap->getArticle()->getId()) === null) {
            $this->flashMessenger()->error(
                'Error',
                'No mapping was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action'   => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $articleMap;
    }
}
