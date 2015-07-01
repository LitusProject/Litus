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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin;

use CudiBundle\Entity\Article\External,
    CudiBundle\Entity\Article\History,
    CudiBundle\Entity\Article\Internal,
    CudiBundle\Entity\Article\SubjectMap,
    CudiBundle\Entity\Comment\Mapping as CommentMapping,
    CudiBundle\Entity\Log\Article\SubjectMap\Added as SubjectMapAddedLog,
    Cudibundle\Entity\Article,
    Zend\View\Model\ViewModel;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();

        if (null !== $this->getParam('field')) {
            $articles = $this->search();
        }

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findAllQuery();
        }

        $paginator = $this->paginator()->createFromQuery(
            $articles,
            $this->getParam('page')
        );

        foreach ($paginator as $item) {
            $item->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('cudi_article_add');
        $academicYear = $this->getAcademicYearEntity();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $article = $form->hydrateObject();
                $formData = $form->getData();

                $this->getEntityManager()->persist($article);

                if ($formData['article']['type'] != 'common') {
                    $subject = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject')
                        ->findOneById($formData['subject_form']['subject']['id']);

                    $mapping = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                        ->findOneByArticleAndSubjectAndAcademicYear($article, $subject, $academicYear);

                    if (null === $mapping) {
                        $mapping = new SubjectMap($article, $subject, $academicYear, $formData['subject_form']['mandatory']);
                        $this->getEntityManager()->persist($mapping);
                        $this->getEntityManager()->persist(new SubjectMapAddedLog($this->getAuthentication()->getPersonObject(), $mapping));
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The article was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_article',
                    array(
                        'action' => 'edit',
                        'id' => $article->getId(),
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'currentAcademicYear' => $academicYear,
            )
        );
    }

    public function editAction()
    {
        if (!($article = $this->getArticleEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_article_edit', array('article' => $article));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            // make a history before changing the article
            $history = new History($article);

            if ($form->isValid()) {
                $this->getEntityManager()->persist($history);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The article was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_article',
                    array(
                        'action' => 'edit',
                        'id' => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $saleArticle = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneByArticle($article);

        $comments = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comment\Mapping')
            ->findByArticle($article);

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
                'saleArticle' => $saleArticle,
                'comments' => $comments,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($article = $this->getArticleEntity())) {
            return new ViewModel();
        }

        $article->setIsHistory(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function historyAction()
    {
        if (!($article = $this->getArticleEntity())) {
            return new ViewModel();
        }

        $history = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\History')
            ->findAllByArticle($article);

        return new ViewModel(
            array(
                'history' => $history,
                'current' => $article,
            )
        );
    }

    public function searchAction()
    {
        $academicYear = $this->getAcademicYearEntity();

        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $articles = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($articles as $article) {
            $article->setEntityManager($this->getEntityManager());

            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getTitle();
            $item->author = $article->getAuthors();
            $item->isbn = $article->getISBN() ? $article->getISBN() : '';
            $item->publisher = $article->getPublishers();
            $item->yearPublished = $article->getYearPublished() ? $article->getYearPublished() : '';
            $item->isInternal = $article->isInternal();
            $item->saleArticle = $article->getSaleArticle($academicYear) ? $article->getSaleArticle($academicYear)->getId() : 0;
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function duplicateAction()
    {
        $academicYear = $this->getAcademicYearEntity();

        if (!($article = $this->getArticleEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_article_duplicate', array('article' => $article));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $new = $form->hydrateObject();

                $new->setType($article->getType());

                $this->getEntityManager()->persist($new);

                $mappings = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                    ->findAllByArticleAndAcademicYear($article, $academicYear);

                foreach ($mappings as $mapping) {
                    $this->getEntityManager()->persist(new SubjectMap($new, $mapping->getSubject(), $academicYear, $mapping->isMandatory()));
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The new version of the article was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_article',
                    array(
                        'action' => 'edit',
                        'id' => $article->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
            )
        );
    }

    public function convertToExternalAction()
    {
        if (!($previous = $this->getArticleEntity())) {
            return new ViewModel();
        }

        if (!$previous->isInternal()) {
            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'edit',
                    'id' => $previous->getId(),
                )
            );

            return new ViewModel();
        }

        $article = new External();
        $article->setTitle($previous->getTitle())
            ->setAuthors($previous->getAuthors())
            ->setPublishers($previous->getPublishers())
            ->setYearPublished($previous->getYearPublished())
            ->setISBN($previous->getISBN())
            ->setUrl($previous->getUrl())
            ->setType($previous->getType())
            ->setIsDownloadable($previous->isDownloadable())
            ->setIsSameAsPreviousYear($previous->isSameAsPreviousYear())
            ->setVersionNumber($previous->getVersionNumber());
        $this->getEntityManager()->persist($article);

        $previous->setEntityManager($this->getEntityManager());

        $history = new History($article, $previous);
        $this->getEntityManager()->persist($history);

        $saleArticle = $previous->getSaleArticle();
        $saleArticle->setMainArticle($article);

        $completeHistory = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\History')
            ->findByArticle($previous);

        foreach ($completeHistory as $item) {
            $item->setArticle($article);
        }

        $comments = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comment\Comment')
            ->findAllByArticle($previous);

        foreach ($comments as $comment) {
            $this->getEntityManager()->persist(new CommentMapping($article, $comment));
        }

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticle($previous, true);

        foreach ($mappings as $mapping) {
            $new = new SubjectMap($article, $mapping->getSubject(), $mapping->getAcademicYear(), $mapping->isMandatory());
            $new->setIsProf($mapping->isProf());
            $this->getEntityManager()->persist($new);
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The article was succesfully converted!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_article',
            array(
                'action' => 'edit',
                'id' => $article->getId(),
            )
        );

        return new ViewModel();
    }

    public function convertToInternalAction()
    {
        if (!($previous = $this->getArticleEntity())) {
            return new ViewModel();
        }

        if ($previous->isInternal()) {
            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'edit',
                    'id' => $previous->getId(),
                )
            );

            return new ViewModel();
        }

        $binding = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\Option\Binding')
            ->findOneByCode('glued');

        $frontColor = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\Option\Color')
            ->findOneByName('White');

        $article = new Internal();
        $article->setTitle($previous->getTitle())
            ->setAuthors($previous->getAuthors())
            ->setPublishers($previous->getPublishers())
            ->setYearPublished($previous->getYearPublished())
            ->setISBN($previous->getISBN())
            ->setUrl($previous->getUrl())
            ->setType($previous->getType())
            ->setIsDownloadable($previous->isDownloadable())
            ->setIsSameAsPreviousYear($previous->isSameAsPreviousYear())
            ->setNbBlackAndWhite(0)
            ->setNbColored(0)
            ->setBinding($binding)
            ->setIsOfficial(true)
            ->setIsRectoVerso(true)
            ->setFrontColor($frontColor)
            ->setIsPerforated(false)
            ->setIsColored(false)
            ->setIsHardCovered(false)
            ->setVersionNumber($previous->getVersionNumber());
        $this->getEntityManager()->persist($article);

        $previous->setEntityManager($this->getEntityManager());

        $history = new History($article, $previous);
        $this->getEntityManager()->persist($history);

        $saleArticle = $previous->getSaleArticle();
        $saleArticle->setMainArticle($article);

        $completeHistory = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\History')
            ->findByArticle($previous);

        foreach ($completeHistory as $item) {
            $item->setArticle($article);
        }

        $comments = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comment\Comment')
            ->findAllByArticle($previous);

        foreach ($comments as $comment) {
            $this->getEntityManager()->persist(new CommentMapping($article, $comment));
        }

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticle($previous, true);

        foreach ($mappings as $mapping) {
            $new = new SubjectMap($article, $mapping->getSubject(), $mapping->getAcademicYear(), $mapping->isMandatory());
            $new->setIsProf($mapping->isProf());
            $this->getEntityManager()->persist($new);
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The article was succesfully converted!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_article',
            array(
                'action' => 'edit',
                'id' => $article->getId(),
            )
        );

        return new ViewModel();
    }

    /**
     * @return \Doctrine\ORM\Query|null
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByTitleQuery($this->getParam('string'));
            case 'author':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByAuthorQuery($this->getParam('string'));
            case 'isbn':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByIsbnQuery($this->getParam('string'));
            case 'publisher':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByPublisherQuery($this->getParam('string'));
            case 'subject':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllBySubjectQuery($this->getParam('string'), $this->getAcademicYearEntity());
        }
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
                'cudi_admin_stock',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }
}
