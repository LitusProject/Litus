<?php

namespace CudiBundle\Controller\Admin\Article;

use CommonBundle\Component\Util\File\TmpFile;
use CudiBundle\Component\Document\Generator\Front as FrontGenerator;
use CudiBundle\Entity\Article\Internal as InternalArticle;
use CudiBundle\Entity\File\ArticleMap;
use CudiBundle\Entity\File\File;
use CudiBundle\Entity\Sale\Article as SaleArticle;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * FileController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FileController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $saleArticle = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneByArticle($article);

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\File\ArticleMap')
                ->findAllByArticleQuery($article),
            $this->getParam('page')
        );

        $form = $this->getForm('cudi_article_file_add');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'cudi_admin_article_file',
                array(
                    'action' => 'upload',
                    'id'     => $article->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'form'              => $form,
                'article'           => $article,
                'saleArticle'       => $saleArticle,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function uploadAction()
    {
        $this->initAjax();

        $article = $this->getArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_article_file_add');
        $form->setData(
            array_merge(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            )
        );

        if ($form->isValid()) {
            $formData = $form->getData();

            $filePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.file_path');

            do {
                $fileName = '/' . sha1(uniqid());
            } while (file_exists($filePath . $fileName));

            rename($formData['file']['tmp_name'], $filePath . $fileName);

            $file = new File(
                $this->getEntityManager(),
                $fileName,
                $formData['file']['name'],
                $formData['description'],
                $article,
                $formData['printable']
            );
            $this->getEntityManager()->persist($file);
            $this->getEntityManager()->flush();

            $mapping = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\File\ArticleMap')
                ->findOneByArticleAndFile($article, $file);

            return new ViewModel(
                array(
                    'status' => 'success',
                    'info'   => array(
                        'info' => (object) array(
                            'name'        => $file->getName(),
                            'description' => $file->getDescription(),
                            'printable'   => $mapping->isPrintable(),
                            'mappingId'   => $mapping->getId(),
                            'id'          => $file->getId(),
                        ),
                    ),
                )
            );
        } else {
            return new ViewModel(
                array(
                    'status' => 'error',
                    'form'   => array(
                        'errors' => $form->getMessages(),
                    ),
                )
            );
        }
    }

    public function editAction()
    {
        $mapping = $this->getFileArticleMapEntity();
        if ($mapping === null) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_article_file_edit', $mapping);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The file was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_article_file',
                    array(
                        'action' => 'manage',
                        'id'     => $mapping->getArticle()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'file'    => $mapping->getFile(),
                'article' => $mapping->getArticle(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $mapping = $this->getFileArticleMapEntity();
        if ($mapping === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($mapping);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function downloadAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.file_path');

        $mapping = $this->getFileArticleMapEntity();
        if ($mapping === null) {
            return new ViewModel();
        }

        $file = $mapping->getFile();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $file->getName() . '"',
                'Content-Type'        => 'application/octet-stream',
                'Content-Length'      => filesize($filePath . $file->getPath()),
            )
        );
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath . $file->getPath(), 'r');
        $data = fread($handle, filesize($filePath . $file->getPath()));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    public function frontAction()
    {
        $article = $this->getSaleArticleEntity();
        if ($article === null) {
            return new ViewModel();
        }

        $file = new TmpFile();
        $document = new FrontGenerator($this->getEntityManager(), $article, $file);
        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="front_'.substr($article->getBarcode(), -5, 5).'.pdf"',
                'Content-Type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    /**
     * @return InternalArticle|null
     */
    private function getArticleEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Article');

        if (!($article instanceof InternalArticle)) {
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

    /**
     * @return SaleArticle|null
     */
    private function getSaleArticleEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Sale\Article');

        if (!($article instanceof SaleArticle)) {
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

    /**
     * @return ArticleMap|null
     */
    private function getFileArticleMapEntity()
    {
        $articleMap = $this->getEntityById('CudiBundle\Entity\File\ArticleMap');

        if (!($articleMap instanceof ArticleMap)) {
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

        return $articleMap;
    }
}
