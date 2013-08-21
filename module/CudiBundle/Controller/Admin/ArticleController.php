<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Article\External,
    CudiBundle\Entity\Article\Internal,
    CudiBundle\Entity\Article\History,
    CudiBundle\Entity\Article\SubjectMap,
    cudiBundle\Entity\Comments\Mapping as CommentMapping,
    CudiBundle\Entity\Log\Article\SubjectMap\Added as SubjectMapAddedLog,
    CudiBundle\Form\Admin\Article\Add as AddForm,
    CudiBundle\Form\Admin\Article\Edit as EditForm,
    CudiBundle\Form\Admin\Article\Duplicate as DuplicateForm,
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
        $academicYear = $this->getAcademicYear();

        if (null !== $this->getParam('field'))
            $articles = $this->_search($academicYear);

        if (!isset($articles)) {
            $articles = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findAll();
        }

        $paginator = $this->paginator()->createFromArray(
            $articles,
            $this->getParam('page')
        );

        foreach($paginator as $item)
            $item->setEntityManager($this->getEntityManager());

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
        $form = new AddForm($this->getEntityManager());
        $academicYear = $this->getAcademicYear();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($formData['internal']) {
                    $binding = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article\Option\Binding')
                        ->findOneById($formData['binding']);

                    $frontColor = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article\Option\Color')
                        ->findOneById($formData['front_color']);

                    $article = new Internal(
                        $formData['title'],
                        $formData['author'],
                        $formData['publisher'],
                        $formData['year_published'],
                        $formData['isbn'] != '' ? $formData['isbn'] : null,
                        $formData['url'],
                        $formData['type'],
                        $formData['downloadable'],
                        $formData['same_as_previous_year'],
                        $formData['nb_black_and_white'],
                        $formData['nb_colored'],
                        $binding,
                        $formData['official'],
                        $formData['rectoverso'],
                        $frontColor,
                        $formData['perforated'],
                        $formData['colored']
                    );
                } else {
                    $article = new External(
                        $formData['title'],
                        $formData['author'],
                        $formData['publisher'],
                        $formData['year_published'],
                        $formData['isbn'] != ''? $formData['isbn'] : null,
                        $formData['url'],
                        $formData['type'],
                        $formData['downloadable'],
                        $formData['same_as_previous_year']
                    );
                }

                $this->getEntityManager()->persist($article);

                if ($formData['type'] != 'common') {
                    if ($formData['subject_id'] == '') {
                        $subject = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Subject')
                            ->findOneByCode($formData['subject']);
                    } else {
                        $subject = $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Subject')
                            ->findOneById($formData['subject_id']);
                    }
                    $mapping = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                        ->findOneByArticleAndSubjectAndAcademicYear($article, $subject, $academicYear);

                    if (null === $mapping) {
                        $mapping = new SubjectMap($article, $subject, $academicYear, $formData['mandatory']);
                        $this->getEntityManager()->persist($mapping);
                        $this->getEntityManager()->persist(new SubjectMapAddedLog($this->getAuthentication()->getPersonObject(), $mapping));
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully created!'
                    )
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
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $article);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $history = new History($article);
                $this->getEntityManager()->persist($history);

                $article->setTitle($formData['title'])
                    ->setAuthors($formData['author'])
                    ->setPublishers($formData['publisher'])
                    ->setYearPublished($formData['year_published'])
                    ->setISBN($formData['isbn'] != ''? $formData['isbn'] : null)
                    ->setURL($formData['url'])
                    ->setIsDownloadable($formData['downloadable'])
                    ->setIsSameAsPreviousYear($formData['same_as_previous_year'])
                    ->setType(isset($formData['type']) ? $formData['type'] : 'common');

                if ($article->isInternal()) {
                    $binding = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article\Option\Binding')
                        ->findOneById($formData['binding']);

                    $frontPageColor = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article\Option\Color')
                        ->findOneById($formData['front_color']);

                    $article->setNbBlackAndWhite($formData['nb_black_and_white'])
                        ->setNbColored($formData['nb_colored'])
                        ->setBinding($binding)
                        ->setIsOfficial($formData['official'])
                        ->setIsRectoVerso($formData['rectoverso'])
                        ->setFrontColor($frontPageColor)
                        ->setIsPerforated($formData['perforated'])
                        ->setIsColored($formData['colored']);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The article was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_article',
                    array(
                        'action' => 'manage'
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

        if (!($article = $this->_getArticle()))
            return new ViewModel();

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
        if (!($article = $this->_getArticle()))
            return new ViewModel();

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
        $academicYear = $this->getAcademicYear();

        $this->initAjax();

        $articles = $this->_search($academicYear);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($articles, $numResults);

        $result = array();
        foreach($articles as $article) {
            $article->setEntityManager($this->getEntityManager());

            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getTitle();
            $item->author = $article->getAuthors();
            $item->isbn = $article->getISBN();
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
        $academicYear = $this->getAcademicYear();

        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $form = new DuplicateForm($this->getEntityManager(), $article);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($formData['internal']) {
                    $binding = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article\Option\Binding')
                        ->findOneById($formData['binding']);

                    $frontColor = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article\Option\Color')
                        ->findOneById($formData['front_color']);

                    $new = new Internal(
                        $formData['title'],
                        $formData['author'],
                        $formData['publisher'],
                        $formData['year_published'],
                        $formData['isbn'] != ''? $formData['isbn'] : null,
                        $formData['url'],
                        $article->getType(),
                        $formData['downloadable'],
                        $formData['same_as_previous_year'],
                        $formData['nb_black_and_white'],
                        $formData['nb_colored'],
                        $binding,
                        $formData['official'],
                        $formData['rectoverso'],
                        $frontColor,
                        $formData['perforated'],
                        $formData['colored']
                    );
                } else {
                    $new = new External(
                        $formData['title'],
                        $formData['author'],
                        $formData['publisher'],
                        $formData['year_published'],
                        $formData['isbn'] != ''? $formData['isbn'] : null,
                        $formData['url'],
                        $article->getType(),
                        $formData['downloadable'],
                        $formData['same_as_previous_year']
                    );
                }

                $this->getEntityManager()->persist($new);

                $mappings = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                    ->findAllByArticleAndAcademicYear($article, $academicYear);

                foreach($mappings as $mapping) {
                    $this->getEntityManager()->persist(new SubjectMap($new, $mapping->getSubject(), $academicYear, $mapping->isMandatory()));
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The new version of the article was successfully created!'
                    )
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
        if (!($previous = $this->_getArticle()))
            return new ViewModel();

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

        $article = new External(
            $previous->getTitle(),
            $previous->getAuthors(),
            $previous->getPublishers(),
            $previous->getYearPublished(),
            $previous->getISBN(),
            $previous->getUrl(),
            $previous->getType(),
            $previous->isDownloadable(),
            $previous->isSameAsPreviousYear()
        );
        $article->setVersionNumber($previous->getVersionNumber());
        $this->getEntityManager()->persist($article);

        $history = new History($article, $previous);
        $this->getEntityManager()->persist($history);

        $completeHistory = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\History')
            ->findByArticle($previous);

        foreach($completeHistory as $item)
            $item->setArticle($article);

        $comments = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comment\Comment')
            ->findAllByArticle($previous);

        foreach($comments as $comment)
            $this->getEntityManager()->persist(new CommentMapping($article, $comment));

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticle($previous, true);

        foreach($mappings as $mapping) {
            $new = new SubjectMap($article, $mapping->getSubject(), $mapping->getAcademicYear(), $mapping->isMandatory());
            $new->setIsProf($mapping->isProf());
            $this->getEntityManager()->persist($new);
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The article was succesfully converted!'
            )
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
        if (!($previous = $this->_getArticle()))
            return new ViewModel();

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

        $article = new Internal(
            $previous->getTitle(),
            $previous->getAuthors(),
            $previous->getPublishers(),
            $previous->getYearPublished(),
            $previous->getISBN(),
            $previous->getUrl(),
            $previous->getType(),
            $previous->isDownloadable(),
            $previous->isSameAsPreviousYear(),
            0,
            0,
            $binding,
            true,
            true,
            $frontColor,
            false,
            false
        );
        $article->setVersionNumber($previous->getVersionNumber());
        $this->getEntityManager()->persist($article);

        $history = new History($article, $previous);
        $this->getEntityManager()->persist($history);

        $completeHistory = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\History')
            ->findByArticle($previous);

        foreach($completeHistory as $item)
            $item->setArticle($article);

        $comments = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Comment\Comment')
            ->findAllByArticle($previous);

        foreach($comments as $comment)
            $this->getEntityManager()->persist(new CommentMapping($article, $comment));

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticle($previous, true);

        foreach($mappings as $mapping) {
            $new = new SubjectMap($article, $mapping->getSubject(), $mapping->getAcademicYear(), $mapping->isMandatory());
            $new->setIsProf($mapping->isProf());
            $this->getEntityManager()->persist($new);
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The article was succesfully converted!'
            )
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

    private function _search(AcademicYear $academicYear)
    {
        switch($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByTitle($this->getParam('string'));
            case 'author':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByAuthor($this->getParam('string'));
            case 'isbn':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByISBN($this->getParam('string'));
            case 'publisher':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllByPublisher($this->getParam('string'));
            case 'subject':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article')
                    ->findAllBySubject($this->getParam('string'), $this->getCurrentAcademicYear());
        }
    }

    private function _getArticle()
    {
        if (null === $this->getParam('id')) {
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
            ->findOneById($this->getParam('id'));

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
}
