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

namespace PublicationBundle\Controller\Admin\Edition;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    PublicationBundle\Entity\Publication,
    PublicationBundle\Entity\Edition\Html as HtmlEdition,
    PublicationBundle\Form\Admin\Edition\Html\Add as AddForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Validator\File\Size as SizeValidator,
    Zend\Validator\File\Extension as ExtensionValidator,
    Zend\View\Model\ViewModel,
    \ZipArchive;

/**
 * HtmlController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class HtmlController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($publication = $this->_getPublication()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Edition\Html')
                ->findAllByPublicationAndAcademicYearQuery($publication, $this->getCurrentAcademicYear()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'publication' => $publication,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        if (!($publication = $this->_getPublication()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager(), $publication, $this->getCurrentAcademicYear());
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'publication_admin_edition_html',
                array(
                    'action' => 'upload',
                    'id' => $publication->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'publication' => $publication,
                'form' => $form,
            )
        );
    }

    public function uploadAction()
    {
        if (!($publication = $this->_getPublication()))
            return new ViewModel();

        $form = new AddForm($this->getEntityManager(), $publication, $this->getCurrentAcademicYear());
        $formData = $this->getRequest()->getPost();
        $form->setData($formData);

        $upload = new FileUpload();
        $upload->setValidators($form->getInputFilter()->get('file')->getValidatorChain()->getValidators());

        if ($form->isValid() && $upload->isValid()) {
            $formData = $form->getFormData($formData);

            $publicFilePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('publication.public_html_directory');
            $filePath = 'public' . $publicFilePath;

            $publicFilePathPdf = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('publication.public_pdf_directory');

            $fileName = '';
            do {
                $fileName = sha1(uniqid());
            } while (file_exists($filePath . $fileName));

            $pdfVersion = $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Edition\Pdf')
                ->findOneById($formData['pdf_version']);

            $host = (('on' === $this->getRequest()->getServer('HTTPS', 'off')) ? 'https' : 'http')
                . '://'
                . $this->getRequest()->getServer('HTTP_HOST');
            $html = preg_replace(
                '/{{[ ]*pdfVersion[ ]*}}/',
                $host . $publicFilePathPdf . $pdfVersion->getFileName(),
                preg_replace(
                    '/{{[ ]*imageUrl[ ]*}}/',
                    $host . $publicFilePath . $fileName,
                    $formData['html']
                )
            );

            $edition = new HtmlEdition(
                $publication,
                $this->getCurrentAcademicYear(),
                $formData['title'],
                $html,
                DateTime::createFromFormat('d/m/Y', $formData['date']),
                $fileName
            );

            if (!file_exists($filePath . $fileName))
                mkdir($filePath . $fileName, 0775, true);

            $zipFileName = $upload->getFileName();
            $upload->receive();

            $zip = new ZipArchive;

            if (true === $zip->open($zipFileName)) {
                $zip->extractTo($filePath . $fileName);
                $zip->close();
                unlink($zipFileName);
            } else {
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::ERROR,
                        'Error',
                        'Something went wrong while extracting the archive!'
                    )
                );

                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info' => array(
                            'info' => (object) array(
                                'title' => 'error',
                            ),
                        ),
                    )
                );
            }

            $this->getEntityManager()->persist($edition);
            $this->getEntityManager()->flush();

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::SUCCESS,
                    'Success',
                    'The publication was succesfully created!'
                )
            );

            return new ViewModel(
                array(
                    'status' => 'success',
                    'info' => array(
                        'info' => (object) array(
                            'title' => $edition->getTitle(),
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

            if (sizeof($upload->getMessages()) > 0)
                $formErrors['file'] = $upload->getMessages();

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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($edition = $this->_getEdition()))
            return new ViewModel();

        $publicFilePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.public_html_directory');
        $filePath = 'public' . $publicFilePath;

        if (file_exists($filePath . $edition->getFileName()))
            $this->_rrmdir($filePath . $edition->getFileName());
        $this->getEntityManager()->remove($edition);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getEdition()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the edition!'
                )
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $edition = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition\Html')
            ->findOneById($this->getParam('id'));

        if (null === $edition) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No edition with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $edition;
    }

    private function _getPublication()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the publication!'
                )
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $publication = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneActiveById($this->getParam('id'));

        if (null === $publication) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No publication with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $publication;
    }

    /**
     * @param string $dir
     */
    private function _rrmdir($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if(is_dir($file))
                $this->_rrmdir($file);
            else
                unlink($file);
            }
        rmdir($dir);
    }
}
