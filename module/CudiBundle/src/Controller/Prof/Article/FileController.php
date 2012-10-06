<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Controller\Prof\Article;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Article,
    CudiBundle\Entity\Files\File,
    CudiBundle\Entity\Prof\Action,
    CudiBundle\Form\Prof\File\Add as AddForm,
    Doctrine\ORM\EntityManager,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * FileController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FileController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Files\Mapping')
            ->findAllByArticle($article, true);

        $fileMappings = array();
        foreach($mappings as $mapping) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('file', $mapping->getId(), 'remove');

            if (!isset($actions[0]))
                $fileMappings[] = $mapping;
        }

        $form = new AddForm();
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'prof_file',
                array(
                    'action' => 'upload',
                    'id' => $article->getId(),
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            )
        );

        return new ViewModel(
            array(
                'form' => $form,
                'article' => $article,
                'mappings' => $fileMappings,
                'uploadProgressName' => ini_get('session.upload_progress.name'),
                'uploadProgressId' => uniqid(),
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

    public function uploadAction()
    {
        if (!($article = $this->_getArticle()))
            return new ViewModel();

        $form = new AddForm();
        $formData = $this->getRequest()->getPost();
        $form->setData($formData);

        if ($form->isValid()) {
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
                false
            );
            $this->getEntityManager()->persist($file);

            $this->getEntityManager()->flush();

            $mapping = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Files\Mapping')
                ->findOneByFile($file);
            $mapping->setIsProf(true);

            $action = new Action($this->getAuthentication()->getPersonObject(), 'file', $mapping->getId(), 'add');
            $this->getEntityManager()->persist($action);

            $this->getEntityManager()->flush();

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
                            'id' => $file->getId(),
                        )
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
                        'errors' => $formErrors
                    ),
                )
            );
        }
    }

    public function progressAction()
    {
        $uploadId = ini_get('session.upload_progress.prefix') . $this->getRequest()->getPost('upload_id');

        return new ViewModel(
            array(
                'result' => isset($_SESSION[$uploadId]) ? $_SESSION[$uploadId] : '',
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->_getFileMapping()))
            return new ViewModel();

        if ($mapping->isProf()) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('file', $mapping->getId(), 'add');
            foreach ($actions as $action)
                $this->getEntityManager()->remove($action);

            $this->getEntityManager()->remove($mapping);
        } else {
            $action = new Action($this->getAuthentication()->getPersonObject(), 'file', $mapping->getId(), 'remove');
            $this->getEntityManager()->persist($action);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getArticle($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

        if (null === $id) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No ID was given to identify the article!'
                )
            );

            $this->redirect()->toRoute(
                'prof_article',
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
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'ERROR',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
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
                    'ERROR',
                    'No ID was given to identify the file!'
                )
            );

            $this->redirect()->toRoute(
                'prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
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
                    'ERROR',
                    'No file with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $file;
    }
}
