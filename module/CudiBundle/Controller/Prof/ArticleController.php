<?php

namespace CudiBundle\Controller\Prof;

use CudiBundle\Entity\Article;
use CudiBundle\Entity\Article\Internal;
use CudiBundle\Entity\Article\SubjectMap;
use CudiBundle\Entity\Prof\Action;
use Laminas\View\Model\ViewModel;
use SyllabusBundle\Entity\Subject;
use SyllabusBundle\Entity\Subject\ProfMap;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findAllByProf($this->getAuthentication()->getPersonObject());

        foreach ($articles as $article) {
            $article->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'articles' => $articles,
            )
        );
    }

    public function addAction()
    {
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_prof_article_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();
                $article = $form->hydrateObject();

                $article->setIsProf(true);
                if ($formData['draft']) {
                    $article->setIsDraft(true);
                }

                $this->getEntityManager()->persist($article);

                $action = new Action($this->getAuthentication()->getPersonObject(), 'article', $article->getId(), 'add');
                $this->getEntityManager()->persist($action);

                $subject = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                    ->findOneBySubjectIdAndProfAndAcademicYear(
                        $formData['subject']['subject']['id'],
                        $this->getAuthentication()->getPersonObject(),
                        $academicYear
                    );

                $mapping = new SubjectMap($article, $subject->getSubject(), $academicYear, $formData['subject']['mandatory']);
                $mapping->setIsProf(true);
                $this->getEntityManager()->persist($mapping);

                $action = new Action($this->getAuthentication()->getPersonObject(), 'mapping', $mapping->getId(), 'add');
                $this->getEntityManager()->persist($action);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The article was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_prof_article',
                    array(
                        'action'   => 'manage',
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        $isPost = false;
        $isInternalPost = false;

        if ($this->getRequest()->isPost()) {
            $isPost = true;
            $isInternalPost = isset($form->getData()['internal']) && $form->getData()['internal'];
        }

        return new ViewModel(
            array(
                'form'           => $form,
                'isPost'         => $isPost,
                'isInternalPost' => $isInternalPost,
            )
        );
    }

    public function addFromSubjectAction()
    {
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $subject = $this->getSubjectEntity();
        if ($subject === null) {
            return new ViewModel();
        }

        $form = $this->getForm(
            'cudi_prof_article_add-with-subject',
            array(
                'subject' => $subject,
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();
                $article = $form->hydrateObject();

                $article->setIsProf(true);
                if ($formData['draft']) {
                    $article->setIsDraft(true);
                }

                $this->getEntityManager()->persist($article);

                $action = new Action($this->getAuthentication()->getPersonObject(), 'article', $article->getId(), 'add');
                $this->getEntityManager()->persist($action);

                $mappingProf = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                    ->findOneBySubjectIdAndProfAndAcademicYear(
                        $subject->getId(),
                        $this->getAuthentication()->getPersonObject(),
                        $academicYear
                    );

                $mapping = new SubjectMap(
                    $article,
                    $mappingProf->getSubject(),
                    $academicYear,
                    $formData['subject']['mandatory'] ?? false
                );
                $mapping->setIsProf(true);
                $this->getEntityManager()->persist($mapping);

                $action = new Action($this->getAuthentication()->getPersonObject(), 'mapping', $mapping->getId(), 'add');
                $this->getEntityManager()->persist($action);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The article was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_prof_subject',
                    array(
                        'action'   => 'subject',
                        'id'       => $subject->getId(),
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'           => $form,
                'subject'        => $subject,
                'isPost'         => $this->getRequest()->isPost(),
                'isInternalPost' => $this->getRequest()->isPost() ? $form->getData()['internal'] : false,
            )
        );
    }

    public function editAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $history = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\History')
            ->findOneByPrecursor($article);

        if (isset($history)) {
            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action' => 'edit',
                    'id'     => $history->getArticle()->getId(),
                )
            );
        }

        $duplicate = clone $article;

        $form = $this->getForm('cudi_prof_article_edit', $duplicate);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                if (!$article->isProf()) {
                    $duplicate->setIsProf(true);
                    $edited = false;

                    if ($article->getTitle() != $duplicate->getTitle()) {
                        $edited = true;
                    } elseif ($article->getAuthors() != $duplicate->getAuthors()) {
                        $edited = true;
                    } elseif ($article->getPublishers() != $duplicate->getPublishers()) {
                        $edited = true;
                    } elseif ($article->getYearPublished() != $duplicate->getYearPublished()) {
                        $edited = true;
                    } elseif ($article->getIsbn() != $duplicate->getIsbn()) {
                        $edited = true;
                    } elseif ($article->getUrl() != $duplicate->getUrl()) {
                        $edited = true;
                    } elseif ($article->isDownloadable() !== $duplicate->isDownloadable()) {
                        $edited = true;
                    } elseif ($article->isSameAsPreviousYear() !== $duplicate->getType()) {
                        $edited = true;
                    } elseif ($article->getType() != $duplicate->getType()) {
                        $edited = true;
                    } elseif ($article instanceof Internal && $duplicate instanceof Internal) {
                        if ($article->getBinding()->getId() != $duplicate->getBinding()->getId()) {
                            $edited = true;
                        } elseif ($article->isRectoVerso() !== $duplicate->isRectoVerso()) {
                            $edited = true;
                        } elseif ($article->isPerforated() !== $duplicate->isPerforated()) {
                            $edited = true;
                        } elseif ($article->isColored() !== $duplicate->isColored()) {
                            $edited = true;
                        }
                    }

                    $duplicate->setIsDraft($formData['draft']);

                    if ($edited) {
                        $this->getEntityManager()->persist($duplicate);
                        $action = new Action($this->getAuthentication()->getPersonObject(), 'article', $duplicate->getId(), 'edit', $article->getId());
                        $this->getEntityManager()->persist($action);
                    }
                } else {
                    $form->hydrateObject($article);

                    $article->setIsDraft($formData['draft']);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The article was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_prof_article',
                    array(
                        'action'   => 'manage',
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'article' => $article,
            )
        );
    }

    public function deleteAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $action = new Action($this->getAuthentication()->getPersonObject(), 'article', $article->getId(), 'delete');
        $this->getEntityManager()->persist($action);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findAllByProf($this->getAuthentication()->getPersonObject());

        $result = array();
        foreach ($articles as $article) {
            $item = (object) array();
            $item->id = $article->getId();
            $item->value = $article->getTitle() . ' - ' . $article->getYearPublished();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
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

        $article = null;

        if ($this->hasAccess()->toResourceAction('cudi_prof_subject', 'all')) {
            $article = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findOneById($id);
        } else {
            $article = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article')
                ->findOneByIdAndProf($id, $this->getAuthentication()->getPersonObject());
        }

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
     * @return Subject|null
     */
    private function getSubjectEntity()
    {
        $academicYear = $this->getCurrentAcademicYear();
        if ($academicYear === null) {
            return;
        }

        $mapping = null;

        if ($this->hasAccess()->toResourceAction('cudi_prof_subject', 'all')) {
            $mapping = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findOneBySubjectIdAndAcademicYear(
                    $this->getParam('id', 0),
                    $academicYear
                );
        } else {
            $mapping = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findOneBySubjectIdAndProfAndAcademicYear(
                    $this->getParam('id', 0),
                    $this->getAuthentication()->getPersonObject(),
                    $academicYear
                );
        }

        if (!($mapping instanceof ProfMap)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject was found!'
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

        return $mapping->getSubject();
    }
}
