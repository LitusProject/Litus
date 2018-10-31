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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile;
use SecretaryBundle\Component\Document\Generator\PhotosZip as PhotosZipGenerator;
use Zend\Http\Headers;
use Zend\View\Model\ViewModel;

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
