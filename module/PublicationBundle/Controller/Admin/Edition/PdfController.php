<?php

namespace PublicationBundle\Controller\Admin\Edition;

use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use PublicationBundle\Entity\Edition\Pdf as PdfEdition;
use PublicationBundle\Entity\Publication;

/**
 * PdfController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class PdfController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('PublicationBundle\Entity\Edition\Pdf')
                ->findAllByPublicationQuery($publication, $this->getCurrentAcademicYear()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'publication'       => $publication,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }

        $form = $this->getForm('publication_edition_pdf_add', array('publication' => $publication));
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'publication_admin_edition_pdf',
                array(
                    'action' => 'upload',
                    'id'     => $publication->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'publication' => $publication,
                'form'        => $form,
            )
        );
    }

    public function uploadAction()
    {
        $publication = $this->getPublicationEntity();
        if ($publication === null) {
            return new ViewModel();
        }

        $form = $this->getForm('publication_edition_pdf_add', array('publication' => $publication));
        $formData = $this->getRequest()->getPost();

        $form->setData(
            array_merge_recursive(
                $formData->toArray(),
                $this->getRequest()->getFiles()->toArray()
            )
        );

        $date = self::loadDate($formData['date']);

        if ($form->isValid() && $date) {
            $formData = $form->getData();

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

            rename($formData['file']['tmp_name'], $filePath . $fileName);
            chmod($filePath.$fileName, 0744); // set permissions

            $this->getEntityManager()->persist($edition);
            $this->getEntityManager()->flush();

            $this->flashMessenger()->success(
                'Success',
                'The publication was succesfully created!'
            );

            return new ViewModel(
                array(
                    'status' => 'success',
                    'info'   => array(
                        'info' => (object) array(
                            'title' => $edition->getTitle(),
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

    public function deleteAction()
    {
        $this->initAjax();

        $edition = $this->getPdfEditionEntity();
        if ($edition === null) {
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
        $edition = $this->getPdfEditionEntity();
        if ($edition === null) {
            return new ViewModel();
        }

        $filePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.public_pdf_directory');

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $edition->getTitle() . '"',
                'Content-Type'        => 'application/pdf',
                'Content-Length'      => filesize($filePath . $edition->getFileName()),
            )
        );
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
     * @return PdfEdition|null
     */
    private function getPdfEditionEntity()
    {
        $edition = $this->getEntityById('PublicationBundle\Entity\Edition\Pdf');

        if (!($edition instanceof PdfEdition)) {
            $this->flashMessenger()->error(
                'Error',
                'No edition was found!'
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

    /**
     * @return Publication|null
     */
    private function getPublicationEntity()
    {
        $publication = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneActiveById($this->getParam('id'));

        if (!($publication instanceof Publication)) {
            $this->flashMessenger()->error(
                'Error',
                'No publication was found!'
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
     * @param  string $date
     * @return DateTime|null
     */
    private static function loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y', $date) ?: null;
    }
}
