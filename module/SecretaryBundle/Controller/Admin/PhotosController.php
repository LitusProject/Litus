<?php

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use SecretaryBundle\Component\Document\Generator\PhotosZip as PhotosZipGenerator;

/**
 * PhotosController
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class PhotosController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function photosAction()
    {
        $form = $this->getForm('secretary_photos_photos');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'secretary_admin_photos',
                array(
                    'action' => 'download',
                )
            )
        );

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function downloadAction()
    {
        $form = $this->getForm('secretary_photos_photos');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $academicYear = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\AcademicYear')
                    ->findOneById($formData['academic_year']);

                $promotions = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Promotion')
                    ->findAllByAcademicYear($academicYear);

                $archive = new TmpFile();

                $zip = new PhotosZipGenerator($this->getEntityManager(), $promotions);
                $zip->generateArchive($archive);

                $headers = new Headers();
                $headers->addHeaders(
                    array(
                        'Content-Disposition' => 'inline; filename="promotions_' . $academicYear->getCode() . '.zip"',
                        'Content-Type'        => mime_content_type($archive->getFileName()),
                        'Content-Length'      => filesize($archive->getFileName()),
                    )
                );

                $this->getResponse()->setHeaders($headers);

                return new ViewModel(
                    array(
                        'data' => $archive->getContent(),
                    )
                );
            }

            $this->redirect()->toRoute(
                'secretary_admin_photos',
                array(
                    'action' => 'photos',
                )
            );
        }

        $this->redirect()->toRoute(
            'secretary_admin_photos',
            array(
                'action' => 'photos',
            )
        );
    }
}
