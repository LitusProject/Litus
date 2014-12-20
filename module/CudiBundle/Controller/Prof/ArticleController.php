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

namespace CudiBundle\Controller\Prof;

use CudiBundle\Entity\Article,
    CudiBundle\Entity\Article\Internal,
    CudiBundle\Entity\Article\SubjectMap,
    CudiBundle\Entity\Prof\Action,
    Zend\View\Model\ViewModel;

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
        if (!($academicYear = $this->getCurrentAcademicYear())) {
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
                    ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
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
                        'action' => 'manage',
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'isPost' => $this->getRequest()->isPost(),
                'isInternalPost' => isset($form->getData()['internal']) && $form->getData()['internal'] ? true : false,
            )
        );
    }

    public function addFromSubjectAction()
    {
        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return new ViewModel();
        }

        if (!($subject = $this->_getSubject())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_prof_article_add-with-subject', array(
            'subject' => $subject,
        ));

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
                    ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                    ->findOneBySubjectIdAndProfAndAcademicYear(
                        $subject->getId(),
                        $this->getAuthentication()->getPersonObject(),
                        $academicYear
                    );

                $mapping = new SubjectMap(
                    $article,
                    $mappingProf->getSubject(),
                    $academicYear,
                    isset($formData['subject']['mandatory']) ? $formData['subject']['mandatory'] : false
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
                        'action' => 'subject',
                        'id' => $subject->getId(),
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'subject' => $subject,
                'isPost' => $this->getRequest()->isPost(),
                'isInternalPost' => isset($formData) && $formData['internal'] ? true : false,
            )
        );
    }

    public function editAction()
    {
        if (!($article = $this->_getArticle())) {
            return new ViewModel();
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
                    } elseif ($article->getYearPublished() != $formData['year_published']) {
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
                    } elseif ($article instanceof Internal) {
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

                    $duplicate->setIsDraft($formData['draft'] ? true : false);

                    if ($edited) {
                        $this->getEntityManager()->persist($duplicate);
                        $action = new Action($this->getAuthentication()->getPersonObject(), 'article', $duplicate->getId(), 'edit', $article->getId());
                        $this->getEntityManager()->persist($action);
                    }
                } else {
                    $form->hydrateObject($article);

                    $article->setIsDraft($formData['draft'] ? true : false);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The article was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_prof_article',
                    array(
                        'action' => 'manage',
                        'language' => $this->getLanguage()->getAbbrev(),
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

    public function deleteAction()
    {
        if (!($article = $this->_getArticle())) {
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
     * @return Article|null
     */
    private function _getArticle($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

        if (null === $id) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the article!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneByIdAndProf($id, $this->getAuthentication()->getPersonObject());

        if (null === $article) {
            $this->flashMessenger()->error(
                'Error',
                'No article with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return \SyllabusBundle\Entity\Subject|null
     */
    private function _getSubject()
    {
        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return;
        }

        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the subject!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $this->getParam('id'),
                $this->getAuthentication()->getPersonObject(),
                $academicYear
            );

        if (null === $mapping) {
            $this->flashMessenger()->error(
                'Error',
                'No subject with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping->getSubject();
    }
}
