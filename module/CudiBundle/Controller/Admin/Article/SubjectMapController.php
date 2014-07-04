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

namespace CudiBundle\Controller\Admin\Article;

use CudiBundle\Entity\Article\Internal as InternalArticle,
    CudiBundle\Entity\Article\SubjectMap,
    CudiBundle\Entity\Log\Article\SubjectMap\Added as AddedLog,
    CudiBundle\Entity\Log\Article\SubjectMap\Removed as RemovedLog,
    CudiBundle\Form\Admin\Article\Mapping\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * SubjectMapController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectMapController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        if (!($academicYear = $this->getAcademicYear()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

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
                    $this->getEntityManager()->persist(new AddedLog($this->getAuthentication()->getPersonObject(), $mapping));

                    if ($article->isInternal()) {
                        $cachePath = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('cudi.front_page_cache_dir');
                        if (null !== $article->getFrontPage() && file_exists($cachePath . '/' . $article->getFrontPage())) {
                            unlink($cachePath . '/' . $article->getFrontPage());
                            $article->setFrontPage();
                        }
                    }

                    $this->getEntityManager()->flush();
                }

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The mapping was successfully added!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_article_subject',
                    array(
                        'action' => 'manage',
                        'id' => $article->getId(),
                        'academicyear' => $academicYear->getCode(),
                    )
                );
            }
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllByArticleAndAcademicYearQuery($article, $academicYear),
            $this->getParam('page')
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
                'article' => $article,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->_getMapping()))
            return new ViewModel();

        $mapping->setRemoved();
        $this->getEntityManager()->persist(new RemovedLog($this->getAuthentication()->getPersonObject(), $mapping));

        $article = $mapping->getArticle();

        if ($article instanceof InternalArticle) {
            $cachePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.front_page_cache_dir');
            if (null !== $article->getFrontPage() && file_exists($cachePath . '/' . $article->getFrontPage()))
                unlink($cachePath . '/' . $article->getFrontPage());
            $article->setFrontPage();
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return SubjectMap
     */
    private function _getMapping()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the mapping!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_article_subject',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->error(
                'Error',
                'No mapping with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_article_subject',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }

    private function _getArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the article!'
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
            $this->flashMessenger()->error(
                'Error',
                'No article with the given ID was found!'
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
