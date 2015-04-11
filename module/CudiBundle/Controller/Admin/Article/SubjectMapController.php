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

use CudiBundle\Entity\Article,
    CudiBundle\Entity\Article\Internal as InternalArticle,
    CudiBundle\Entity\Article\SubjectMap,
    CudiBundle\Entity\Log\Article\SubjectMap\Added as AddedLog,
    CudiBundle\Entity\Log\Article\SubjectMap\Removed as RemovedLog,
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
        if (!($article = $this->getArticleEntity())) {
            return new ViewModel();
        }

        if (!($academicYear = $this->getAcademicYearEntity())) {
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

        if (!($mapping = $this->getSubjectMappingEntity())) {
            return new ViewModel();
        }

        $mapping->setRemoved();
        $this->getEntityManager()->persist(new RemovedLog($this->getAuthentication()->getPersonObject(), $mapping));

        $article = $mapping->getArticleEntity();

        if ($article instanceof InternalArticle) {
            $cachePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.front_page_cache_dir');

            if (null !== $article->getFrontPage() && file_exists($cachePath . '/' . $article->getFrontPage())) {
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
