<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Article;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Comments\Comment,
    CudiBundle\Form\Admin\Article\Comment\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comments\Mapping')
            ->findByArticle($article);

        $form = new AddForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $comment = new Comment(
                    $this->getEntityManager(),
                    $this->getAuthentication()->getPersonObject(),
                    $article,
                    $formData['text'],
                    $formData['type']
                );

                $this->getEntityManager()->persist($comment);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The comment was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_article_comment',
                    array(
                        'action' => 'manage',
                        'id' => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'article' => $article,
                'form' => $form,
                'mappings' => $mappings,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->_getCommentMapping()))
            return new ViewModel();

        $this->getEntityManager()->remove($mapping);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getArticle($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

        if (null === $id) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneById($id);

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }

    private function _getCommentMapping()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the comment!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $comment = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comments\Mapping')
            ->findOneById($this->getParam('id'));

        if (null === $comment) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No comment with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $comment;
    }
}
