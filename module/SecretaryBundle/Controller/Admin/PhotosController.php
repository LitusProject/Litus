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

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Entity\General\AcademicYear,
    DateTime,
    SecretaryBundle\Entity\Promotion,
    SecretaryBundle\Form\Admin\Photos\Photos as PhotosForm,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel,
    ZipArchive;

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

                $zip = new ZipArchive();
                $now = new DateTime();

                $zip->open($archive->getFileName(), ZIPARCHIVE::CREATE);
                $zip->addFromString('GENERATED', $now->format('YmdHi') . PHP_EOL);
                $zip->close();

                $filePath = 'public' . $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path') . '/';

                foreach ($promotions as $promotion) {

                    if ($promotion->getAcademic()->getPhotoPath()) {

                        $extension = $this->_getExtension($filePath . $promotion->getAcademic()->getPhotoPath());

                        $zip->open($archive->getFileName(), ZIPARCHIVE::CREATE);
                        $zip->addFile(
                            $filePath . $promotion->getAcademic()->getPhotoPath(),
                            $promotion->getAcademic()->getFirstName() . '_' . $promotion->getAcademic()->getLastName() . $extension
                        );
                        $zip->close();
                    }

                }

                $headers = new Headers();
                $headers->addHeaders(array(
                    'Content-Disposition' => 'inline; filename="promotions_' . $academicYear->getCode() . '.zip"',
                    'Content-Type'        => mime_content_type($archive->getFileName()),
                    'Content-Length'      => filesize($archive->getFileName()),
                ));

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

    /**
     * returns the extension of the given file. Based on the constant int output of exif_imagetype
     */
    private function _getExtension($fileName)
    {
        $fileType = exif_imagetype ($fileName);
        $result = '';

        switch ($fileType) {
            case 1:
                $result = '.gif';
                break;
            case 2:
                $result = '.jpeg';
                break;
            case 3:
                $result = '.png';
                break;
            case 5:
                $result = '.psd';
                break;
            case 6:
                $result = '.bmp';
                break;
        }

        return $result;
    }
}
