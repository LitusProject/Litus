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

namespace CudiBundle\Controller\Prof\Article;

use CudiBundle\Entity\Article;
use CudiBundle\Entity\Comment\Comment;
use CudiBundle\Entity\Comment\Mapping;
use Zend\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        if (!($article = $this->getArticleEntity())) {
            return new ViewModel();
        }

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comment\Mapping')
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

        if (!($mapping = $this->getCommentMappingEntity())) {
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
     * @param  int|null     $id
     * @return Article|null
     */
    private function getArticleEntity($id = null)
    {
        $id = $id === null ? $this->getParam('id', 0) : $id;

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
     * @return Mapping|null
     */
    private function getCommentMappingEntity()
    {
        $mapping = $this->getEntityById('CudiBundle\Entity\Comment\Mapping');

        if (!($mapping instanceof Mapping) || null === $this->getArticleEntity($mapping->getArticle()->getId())) {
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

        return $mapping;
    }
}
