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

use CommonBundle\Component\Util\File\TmpFile,
    CudiBundle\Component\Document\Generator\Front as FrontGenerator,
    CudiBundle\Entity\File\File,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * FileController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FileController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($article = $this->_getArticle())) {
            return new ViewModel();
        }

        $saleArticle = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneByArticle($article);

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\File\Mapping')
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
                    'id' => $article->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
                'saleArticle' => $saleArticle,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function uploadAction()
    {
        $this->initAjax();

        if (!($article = $this->_getArticle())) {
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
                ->getRepository('Cudibundle\Entity\File\Mapping')
                ->findOneByArticleAndFile($article, $file);

            return new ViewModel(
                array(
                    'status' => 'success',
                    'info' => array(
                        'info' => (object) array(
                            'name' => $file->getName(),
                            'description' => $file->getDescription(),
                            'printable' => $mapping->isPrintable(),
                            'mappingId' => $mapping->getId(),
                            'id' => $file->getId(),
                        ),
                    ),
                )
            );
        } else {
            return new ViewModel(
                array(
                    'status' => 'error',
                    'form' => array(
                        'errors' => $form->getMessages(),
                    ),
                )
            );
        }
    }

    public function editAction()
    {
        if (!($mapping = $this->_getFileMapping())) {
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
                        'id' => $mapping->getArticle()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'file' => $mapping->getFile(),
                'article' => $mapping->getArticle(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->_getFileMapping())) {
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

        if (!($mapping = $this->_getFileMapping())) {
            return new ViewModel();
        }

        $file = $mapping->getFile();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $file->getName() . '"',
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => filesize($filePath . $file->getPath()),
        ));
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
        if (!($article = $this->_getSaleArticle())) {
            return new ViewModel();
        }

        $file = new TmpFile();
        $document = new FrontGenerator($this->getEntityManager(), $article, $file);
        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="front.pdf"',
            'Content-Type'        => 'application/pdf',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
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
                    'action' => 'manage',
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
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    private function _getSaleArticle()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the article!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->error(
                'Error',
                'No article with the given ID was found!'
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
     * @return \CudiBundle\Entity\File\Mapping
     */
    private function _getFileMapping()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the file!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $file = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\File\Mapping')
            ->findOneById($this->getParam('id'));

        if (null === $file) {
            $this->flashMessenger()->error(
                'Error',
                'No file with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_article',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $file;
    }
}
