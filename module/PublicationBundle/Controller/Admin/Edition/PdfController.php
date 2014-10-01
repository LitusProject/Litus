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

use DateTime,
    PublicationBundle\Entity\Edition\Pdf as PdfEdition,
    PublicationBundle\Entity\Publication,
    PublicationBundle\Form\Admin\Edition\Pdf\Add as AddForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\Http\Headers,
    Zend\InputFilter\InputInterface,
    Zend\View\Model\ViewModel;

/**
 * PdfController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class PdfController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($publication = $this->_getPublication())) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Edition\Pdf')
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
        if (!($publication = $this->_getPublication())) {
            return new ViewModel();
        }

        $form = new AddForm($this->getEntityManager(), $publication, $this->getCurrentAcademicYear());
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'publication_admin_edition_pdf',
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
        if (!($publication = $this->_getPublication())) {
            return new ViewModel();
        }

        $form = new AddForm($this->getEntityManager(), $publication, $this->getCurrentAcademicYear());
        $formData = $this->getRequest()->getPost();
        $form->setData($formData);

        $upload = new FileUpload();
        $inputFilter = $form->getInputFilter()->get('file');
        if ($inputFilter instanceof InputInterface) {
            $upload->setValidators($inputFilter->getValidatorChain()->getValidators());
        }

        $date = self::_loadDate($formData['date']);

        if ($form->isValid() && $upload->isValid() && $date) {
            $formData = $form->getFormData($formData);

            $filePath = 'public' . $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('publication.public_pdf_directory');

            do {
                $fileName = sha1(uniqid()) . '.pdf';
            } while (file_exists($filePath . $fileName));

            $edition = new PdfEdition(
                $publication,
                $this->getCurrentAcademicYear(),
                $formData['title'],
                $date,
                $fileName
            );

            $upload->addFilter('Rename', $filePath . $fileName);
            $upload->receive();

            $this->getEntityManager()->persist($edition);
            $this->getEntityManager()->flush();

            $this->flashMessenger()->success(
                'Success',
                'The publication was succesfully created!'
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
                if (!isset($errors[$element->getName()])) {
                    continue;
                }

                $formErrors[$element->getAttribute('id')] = array();

                foreach ($errors[$element->getName()] as $error) {
                    $formErrors[$element->getAttribute('id')][] = $error;
                }
            }

            if (sizeof($upload->getMessages()) > 0) {
                $formErrors['file'] = $upload->getMessages();
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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($edition = $this->_getEdition())) {
            return new ViewModel();
        }

        $filePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.public_pdf_directory');

        if (file_exists($filePath . $edition->getFileName())) {
            unlink($filePath . $edition->getFileName());
        }

        $this->getEntityManager()->remove($edition);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function viewAction()
    {
        if (!($edition = $this->_getEdition())) {
            return new ViewModel();
        }

        $filePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.public_pdf_directory');

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $edition->getTitle() . '"',
            'Content-Type' => 'application/pdf',
            'Content-Length' => filesize($filePath . $edition->getFileName()),
        ));
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath . $edition->getFileName(), 'r');
        $data = fread($handle, filesize($filePath . $edition->getFileName()));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    /**
     * @return resource
     */
    private function _getEdition()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the edition!'
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $edition = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition\Pdf')
            ->findOneById($this->getParam('id'));

        if (null === $edition) {
            $this->flashMessenger()->error(
                'Error',
                'No edition with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $edition;
    }
    private function _getPublication()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the publication!'
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $publication = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneActiveById($this->getParam('id'));

        if (null === $publication) {
            $this->flashMessenger()->error(
                'Error',
                'No publication with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'publication_admin_publication',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $publication;
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y', $date) ?: null;
    }
}
