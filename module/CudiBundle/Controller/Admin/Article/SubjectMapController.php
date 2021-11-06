<?php

namespace CudiBundle\Controller\Admin\Article;

use CudiBundle\Entity\Article;
use CudiBundle\Entity\Article\Internal as InternalArticle;
use CudiBundle\Entity\Article\SubjectMap;
use CudiBundle\Entity\Log\Article\SubjectMap\Added as AddedLog;
use CudiBundle\Entity\Log\Article\SubjectMap\Removed as RemovedLog;
use Laminas\View\Model\ViewModel;

/**
 * SubjectMapController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectMapController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_article_mapping_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $subject = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Subject')
                    ->findOneById($formData['subject']['id']);

                $mapping = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                    ->findOneByArticleAndSubjectAndAcademicYear($article, $subject, $academicYear);

                if ($mapping === null) {
                    $mapping = new SubjectMap($article, $subject, $academicYear, $formData['mandatory']);
                    $this->getEntityManager()->persist($mapping);
                    $this->getEntityManager()->persist(new AddedLog($this->getAuthentication()->getPersonObject(), $mapping));

                    if ($article->isInternal()) {
                        $cachePath = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('cudi.front_page_cache_dir');
                        if ($article->getFrontPage() !== null && file_exists($cachePath . '/' . $article->getFrontPage())) {
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
                        'action'       => 'manage',
                        'id'           => $article->getId(),
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
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'form'                => $form,
                'article'             => $article,
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $mapping = $this->getSubjectMappingEntity();
        if ($mapping === null) {
            return new ViewModel();
        }

        $mapping->remove();
        $this->getEntityManager()->persist(new RemovedLog($this->getAuthentication()->getPersonObject(), $mapping));

        $article = $mapping->getArticle();

        if ($article instanceof InternalArticle) {
            $cachePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.front_page_cache_dir');

            if ($article->getFrontPage() !== null && file_exists($cachePath . '/' . $article->getFrontPage())) {
                unlink($cachePath . '/' . $article->getFrontPage());
            }
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
     * @return SubjectMap|null
     */
    private function getSubjectMappingEntity()
    {
        $mapping = $this->getEntityById('CudiBundle\Entity\Article\SubjectMap');

        if (!($mapping instanceof SubjectMap)) {
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

        return $mapping;
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
}
