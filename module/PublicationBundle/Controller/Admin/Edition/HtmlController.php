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

namespace PublicationBundle\Controller\Admin\Edition;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    PublicationBundle\Entity\Publication,
    PublicationBundle\Entity\Editions\Html as HtmlEdition,
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

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Editions\Html')
                ->findAllByPublicationAndAcademicYear($publication, $this->getCurrentAcademicYear()),
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
                'uploadProgressName' => ini_get('session.upload_progress.name'),
                'uploadProgressId' => uniqid(),
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
        $upload->addValidator(new SizeValidator(array('max' => '30MB')));
        $upload->addValidator(new ExtensionValidator('zip'));

        if ($form->isValid() && $upload->isValid()) {
            $formData = $form->getFormData($formData);
            $edition = new HtmlEdition($publication, $this->getCurrentAcademicYear(), $formData['title'], $formData['html'], DateTime::createFromFormat('d/m/Y', $formData['date']));

            if (!file_exists($edition->getImagesDirectory()))
                mkdir($edition->getImagesDirectory(), 0775, true);

            $filename = $upload->getFileName();
            $upload->receive();

            $zip = new ZipArchive;

            if (true === $zip->open($filename)) {
                $zip->extractTo($edition->getImagesDirectory());
                $zip->close();
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
                    'SUCCES',
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

    public function progressAction()
    {
        $uploadId = ini_get('session.upload_progress.prefix') . $this->getRequest()->getPost()->get('upload_id');

        return new ViewModel(
            array(
                'result' => isset($_SESSION[$uploadId]) ? $_SESSION[$uploadId] : '',
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($edition = $this->_getEdition()))
            return new ViewModel();

        $this->_rrmdir($edition->getImagesDirectory());
        $this->getEntityManager()->remove($edition);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
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
            ->getRepository('PublicationBundle\Entity\Editions\Html')
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

    private function _rrmdir($dir)
    {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                $this->_rrmdir($file);
            else
                unlink($file);
            }
        rmdir($dir);
    }
}
