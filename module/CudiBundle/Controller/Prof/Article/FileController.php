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

namespace CudiBundle\Controller\Prof\Article;

use CudiBundle\Entity\Article\Internal as InternalArticle,
    CudiBundle\Entity\File\File,
    CudiBundle\Entity\File\Mapping,
    CudiBundle\Entity\Prof\Action,
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
        if (!($article = $this->getInternalArticleEntity())) {
            return new ViewModel();
        }

        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\File\Mapping')
            ->findAllByArticle($article, true);

        $fileMappings = array();
        foreach ($mappings as $mapping) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('file', $mapping->getId(), 'remove');

            if (!isset($actions[0])) {
                $fileMappings[] = $mapping;
            }
        }

        $form = $this->getForm('cudi_prof_file_add');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'cudi_prof_file',
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
            )
        );
    }

    public function downloadAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.file_path');

        if (!($mapping = $this->getFileMappingEntity())) {
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

    public function uploadAction()
    {
        if (!($article = $this->getInternalArticleEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_prof_file_add');
        $form->setData(array_merge(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        ));

        if ($form->isValid()) {
            $formData = $form->getData();

            $filePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.file_path');

            $originalName = $formData['file']['name'];

            do {
                $fileName = '/' . sha1(uniqid());
            } while (file_exists($filePath . $fileName));

            rename($formData['file']['tmp_name'], $filePath . $fileName);

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
                ->getRepository('CudiBundle\Entity\File\Mapping')
                ->findOneByFile($file);
            $mapping->setIsProf(true);

            $action = new Action($this->getAuthentication()->getPersonObject(), 'file', $mapping->getId(), 'add');
            $this->getEntityManager()->persist($action);

            $this->getEntityManager()->flush();

            return new ViewModel(
                array(
                    'status' => 'success',
                    'info' => array(
                        'info' => (object) array(
                            'name' => $file->getName(),
                            'description' => $file->getDescription(),
                            'id' => $file->getId(),
                            'mappingId' => $mapping->getId(),
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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($mapping = $this->getFileMappingEntity())) {
            return new ViewModel();
        }

        if ($mapping->isProf()) {
            $actions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndEntityIdAndAction('file', $mapping->getId(), 'add');
            foreach ($actions as $action) {
                $this->getEntityManager()->remove($action);
            }

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

    /**
     * @return InternalArticle|null
     */
    private function getInternalArticleEntity()
    {
        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findOneByIdAndProf($this->getParam('id', 0), $this->getAuthentication()->getPersonObject());

        if (!($article instanceof InternalArticle)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
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
     * @return Mapping|null
     */
    private function getFileMappingEntity()
    {
        $mapping = $this->getEntityById('CudiBundle\Entity\File\Mapping');

        if (!($mapping instanceof Mapping)) {
            $this->flashMessenger()->error(
                'Error',
                'No mapping was found!'
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

        return $mapping;
    }
}
