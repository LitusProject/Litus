<?php

namespace GalleryBundle\Controller\Admin;

use GalleryBundle\Entity\Album;
use GalleryBundle\Entity\Album\Photo;
use Imagick;
use ImagickPixel;
use Laminas\Validator\File\IsImage as IsImageValidator;
use Laminas\Validator\File\Size as SizeValidator;
use Laminas\Validator\File\UploadFile as UploadFileValidator;
use Laminas\Validator\ValidatorChain;
use Laminas\View\Model\ViewModel;

/**
 * GalleryController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GalleryController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'GalleryBundle\Entity\Album',
            $this->getParam('page'),
            array(),
            array(
                'dateActivity' => 'DESC',
            )
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('gallery_album_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $album = $form->hydrateObject();

                $this->getEntityManager()->persist($album);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The album was successfully added!'
                );

                $this->redirect()->toRoute(
                    'gallery_admin_gallery',
                    array(
                        'action' => 'addPhotos',
                        'id'     => $album->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $album = $this->getAlbumEntity();
        if ($album === null) {
            return new ViewModel();
        }

        $form = $this->getForm('gallery_album_edit', array('album' => $album));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The album was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'gallery_admin_gallery',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $album = $this->getAlbumEntity();
        if ($album === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($album);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function photosAction()
    {
        $album = $this->getAlbumEntity();
        if ($album === null) {
            return new ViewModel();
        }

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path');

        $paginator = $this->paginator()->createFromArray(
            $album->getPhotos()->toArray(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'album'             => $album,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'filePath'          => $filePath,
            )
        );
    }

    public function addPhotosAction()
    {
        $album = $this->getAlbumEntity();
        if ($album === null) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'album' => $album,
            )
        );
    }

    public function deletePhotoAction()
    {
        $photo = $this->getPhotoEntity();
        if ($photo === null) {
            return new ViewModel();
        }

        $filePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path') . '/' . $photo->getAlbum()->getId() . '/';

        if (file_exists($filePath . $photo->getFilePath())) {
            unlink($filePath . $photo->getFilePath());
        }
        if (file_exists($filePath . $photo->getThumbPath())) {
            unlink($filePath . $photo->getThumbPath());
        }

        $this->getEntityManager()->remove($photo);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function uploadPosterAction()
    {
        $album = $this->getAlbumEntity();
        if ($album === null) {
            return new ViewModel();
        }

        $form = $this->getForm('gallery_album_poster');
        if ($this->getRequest()->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );

            if ($form->isValid()) {
                $formData = $form->getData();

                $this->receive($formData['poster'], $album);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The event\'s poster has successfully been updated!'
                );

                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info'   => array(
                            'info' => array(
                                'name' => $album->getPoster(),
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

        return new ViewModel(
            array(
                'status' => 'error',
            )
        );
    }

    public function uploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $album = $this->getAlbumEntity();
            if ($album === null) {
                $this->getResponse()->setStatusCode(500);

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'error',
                        ),
                    )
                );
            }

            $validatorChain = new ValidatorChain();
            $validatorChain->attach(new UploadFileValidator());
            $validatorChain->attach(new SizeValidator(array('max' => '15mb')));
            $validatorChain->attach(new IsImageValidator(array('image/jpeg')));

            $file = $this->getRequest()->getFiles()['file'];
            if ($validatorChain->isValid($file)) {
                $filePath = 'public' . $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('gallery.path') . '/' . $album->getId() . '/';

                if (!file_exists($filePath . 'thumbs/')) {
                    if (!file_exists($filePath)) {
                        mkdir($filePath);
                    }

                    mkdir($filePath . 'thumbs/');
                }

                do {
                    $fileName = sha1(uniqid());
                } while (file_exists($filePath . $fileName));

                $image = new Imagick($file['tmp_name']);

                $exif = exif_read_data($file['tmp_name']);
                unlink($file['tmp_name']);

                if (isset($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 1:
                            break;
                        case 2:
                            $image->flopImage();
                            break;
                        case 3:
                            $image->rotateImage(new ImagickPixel(), 180);
                            break;
                        case 4:
                            $image->flipImage();
                            break;
                        case 5:
                            $image->flipImage();
                            $image->rotateImage(new ImagickPixel(), 90);
                            break;
                        case 6:
                            $image->rotateImage(new ImagickPixel(), 90);
                            break;
                        case 7:
                            $image->flopImage();
                            $image->rotateImage(new ImagickPixel(), 90);
                            break;
                        case 8:
                            $image->rotateImage(new ImagickPixel(), -90);
                            break;
                    }
                }

                $image->scaleImage(640, 480, true);
                $thumb = clone $image;
                if ($album->hasWatermark()) {
                    $watermark = new Imagick();
                    $watermark->readImage(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('gallery.watermark_path')
                    );
                    $watermark->scaleImage(57, 48);
                    $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, 0, $image->getImageHeight() - 50);
                }
                $image->writeImage($filePath . $fileName);

                $thumb->cropThumbnailImage(150, 150);
                $thumb->writeImage($filePath . 'thumbs/' . $fileName);

                $photo = new Photo($album, $fileName);
                $this->getEntityManager()->persist($photo);
                $this->getEntityManager()->flush();

                return new ViewModel(
                    array(
                        'result' => array(
                            'status' => 'success',
                        ),
                    )
                );
            }
        }

        $this->getResponse()->setStatusCode(500);

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'error',
                ),
            )
        );
    }

    public function editPosterAction()
    {
        $album = $this->getAlbumEntity();
        if ($album === null) {
            return new ViewModel();
        }

        $form = $this->getForm('gallery_album_poster');
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'gallery_admin_gallery',
                array(
                    'action' => 'uploadPoster',
                    'id'     => $album->getId(),
                )
            )
        );

        return new ViewModel(
            array(
                'album' => $album,
                'form'  => $form,
            )
        );
    }

    private function receive($file, Album $album)
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path');

        $image = new Imagick($file['tmp_name']);
        $image->thumbnailImage(380, 200, true);

        if ($album->getPoster() != '' || $album->getPoster() !== null) {
            $fileName = '/' . $album->getPoster();
        } else {
            do {
                $fileName = '/' . sha1(uniqid());
            } while (file_exists($filePath . $fileName));
        }
        $image->writeImage($filePath . $fileName);

        $album->setPoster($fileName);
    }

    public function censorPhotoAction()
    {
        $photo = $this->getPhotoEntity();
        if ($photo === null) {
            return new ViewModel();
        }

        $photo->setCensored(true);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Succes',
            'The photo was successfully censored!'
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function uncensorPhotoAction()
    {
        $photo = $this->getPhotoEntity();
        if ($photo === null) {
            return new ViewModel();
        }

        $photo->setCensored(false);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Succes',
            'The photo was successfully uncensored!'
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    /**
     * @return Album|null
     */
    private function getAlbumEntity()
    {
        $album = $this->getEntityById('GalleryBundle\Entity\Album');

        if (!($album instanceof Album)) {
            $this->flashMessenger()->error(
                'Error',
                'No album was found!'
            );

            $this->redirect()->toRoute(
                'gallery_admin_gallery',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $album;
    }

    /**
     * @return Photo|null
     */
    private function getPhotoEntity()
    {
        $photo = $this->getEntityById('GalleryBundle\Entity\Album\Photo');

        if (!($photo instanceof Photo)) {
            $this->flashMessenger()->error(
                'Error',
                'No photo was found!'
            );

            $this->redirect()->toRoute(
                'gallery_admin_gallery',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $photo;
    }
}
