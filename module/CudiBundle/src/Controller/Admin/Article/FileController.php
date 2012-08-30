<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Article;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile,
    CudiBundle\Component\Document\Generator\Front as FrontGenerator,
    CudiBundle\Entity\Files\File,
    CudiBundle\Form\Admin\Article\File\Add as AddForm,
    CudiBundle\Form\Admin\Article\File\Edit as EditForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
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
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $saleArticle = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneByArticleAndAcademicYear($article, $this->getAcademicYear());

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Files\Mapping')
            ->findAllByArticle($article);

        $form = new AddForm();
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'admin_article_file',
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
                'mappings' => $mappings,
                'uploadProgressName' => ini_get('session.upload_progress.name'),
                'uploadProgressId' => uniqid(),
            )
        );
    }

    public function uploadAction()
    {
        $this->initAjax();

        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $form = new AddForm();
        $formData = $this->getRequest()->getPost();
        $form->setData($formData);

        if ($form->isValid() && isset($_FILES['file'])) {
            $filePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.file_path');

            $upload = new FileUpload();
            $originalName = $upload->getFileName(null, false);

            $fileName = '';
            do{
                $fileName = '/' . sha1(uniqid());
            } while (file_exists($filePath . $fileName));

            $upload->addFilter('Rename', $filePath . $fileName);
            $upload->receive();

            $file = new File(
                $this->getEntityManager(),
                $fileName,
                $originalName,
                $formData['description'],
                $article,
                $formData['printable']
            );
            $this->getEntityManager()->persist($file);
            $this->getEntityManager()->flush();

            $mapping = $this->getEntityManager()
                ->getRepository('Cudibundle\Entity\Files\Mapping')
                ->findOneByArticleAndFile($article, $file);

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::SUCCESS,
                    'SUCCESS',
                    'The file was successfully uploaded!'
                )
            );

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
            $errors = $form->getMessages();
            $formErrors = array();

            foreach ($form->getElements() as $key => $element) {
                if (!isset($errors[$element->getName()]))
                    continue;

                $formErrors[$element->getAttribute('id')] = array();

                foreach ($errors[$element->getName()] as $error) {
                    $formErrors[$element->getAttribute('id')][] = $error;
                }
            }

            return new ViewModel(
                array(
                    'status' => 'error',
                    'form' => array(
                        'errors' => $formErrors,
                    ),
                )
            );
        }
    }

    public function editAction()
    {
        if (!($mapping = $this->_getFileMapping()))
            return new ViewModel();

        $form = new EditForm($mapping);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $mapping->setPrintable($formData['printable'])
                    ->getFile()->setDescription($formData['description']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The file was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_article_file',
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

        if (!($mapping = $this->_getFileMapping()))
            return new ViewModel();

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

        if (!($mapping = $this->_getFileMapping()))
            return new ViewModel();

        $file = $mapping->getFile();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'inline; filename="' . $file->getName() . '"',
            'Content-type' => 'application/octet-stream',
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

    public function progressAction()
    {
        $uploadId = ini_get('session.upload_progress.prefix') . $this->getRequest()->getPost()->get('upload_id');

        return new ViewModel(
            array(
                'result' => isset($_SESSION[$uploadId]) ? $_SESSION[$uploadId] : '',
            )
        );
    }

    public function frontAction()
    {
        if (!($article = $this->_getSaleArticle()))
            return new ViewModel();

        $file = new TmpFile();
        $document = new FrontGenerator($this->getEntityManager(), $article, $file);
        $document->generate();

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="front.pdf"',
            'Content-type'        => 'application/pdf',
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
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'admin_article',
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
                'admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }

    private function _getSaleArticle()
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
                'admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
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
                'admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $article;
    }

    private function _getFileMapping()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the file!'
                )
            );

            $this->redirect()->toRoute(
                'admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $file = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Files\Mapping')
            ->findOneById($this->getParam('id'));

        if (null === $file) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No file with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_article',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $file;
    }
}
