<?php

namespace GalleryBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    GalleryBundle\Entity\Album\Album,
    GalleryBundle\Entity\Album\Translation,
    GalleryBundle\Entity\Album\Photo,
    GalleryBundle\Form\Admin\Album\Add as AddForm,
    GalleryBundle\Form\Admin\Album\Edit as EditForm,
    Imagick,
    ImagickPixel,
    Zend\Http\Headers,
    Zend\File\Transfer\Transfer as FileTransfer,
    Zend\Validator\File\Size as SizeValidator,
    Zend\Validator\File\IsImage as ImageValidator,
    Zend\View\Model\ViewModel;

/**
 * GalleryController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GalleryController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->crateFromEntity(
            'GalleryBundle\Entity\Album\Album',
            $this->getParam('page'),
            array(),
            array(
                'dateActivity' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $album = new Album($this->getAuthentication()->getPersonObject(), \DateTime::createFromFormat('d#m#Y', $formData['date']));
                $this->getEntityManager()->persist($album);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()]) {
                        $translation = new Translation($album, $language, $formData['title_' . $language->getAbbrev()]);
                        $this->getEntityManager()->persist($translation);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The album was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'gallery_admin_gallery',
                    array(
                        'action' => 'addPhotos',
                        'id' => $album->getId(),
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
        if (!($album = $this->_getAlbum()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $album);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $album->setDate(\DateTime::createFromFormat('d#m#Y', $formData['date']));

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = $album->getTranslation($language);

                    if ($translation) {
                        $translation->setTitle($formData['title_' . $language->getAbbrev()]);
                    } else {
                        $translation = new Translation($album, $language, $formData['title_' . $language->getAbbrev()]);
                        $this->getEntityManager()->persist($translation);
                    }

                    if ($language->getAbbrev() == 'en')
                        $title = $formData['title_' . $language->getAbbrev()];
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The album was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'gallery_admin_gallery',
                    array(
                        'action' => 'manage'
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

        if (!($album = $this->_getAlbum()))
            return new ViewModel();

        $this->getEntityManager()->remove($album);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function photosAction()
    {
        if (!($album = $this->_getAlbum()))
            return new ViewModel();

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path');

        $paginator = $this->paginator()->createFromArray(
            $album->getPhotos()->toArray(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'album' => $album,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'filePath' => $filePath,
            )
        );
    }

    public function addPhotosAction()
    {
        if (!($album = $this->_getAlbum()))
            return new ViewModel();

        return new ViewModel(
            array(
                'album' => $album,
            )
        );
    }

    public function deletePhotoAction()
    {
        if (!($photo = $this->_getPhoto()))
            return new ViewModel();

        $filePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path') . '/' . $photo->getAlbum()->getId() . '/';

        if (file_exists($filePath . $photo->getFilePath()))
            unlink($filePath . $photo->getFilePath());
        if (file_exists($filePath . $photo->getThumbPath()))
            unlink($filePath . $photo->getThumbPath());

        $this->getEntityManager()->remove($photo);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function uploadAction()
    {
        if (!($album = $this->_getAlbum()))
            return new ViewModel();

        $filePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path') . '/' . $album->getId() . '/';

        if (!file_exists($filePath . 'thumbs/')) {
            if (!file_exists($filePath))
                mkdir($filePath);
            mkdir($filePath . 'thumbs/');
        }

        $upload = new FileTransfer();
        $upload->addValidator(new SizeValidator(array('max' => '5MB')));
        $upload->addValidator(new ImageValidator(array('mimeType' => 'image/jpeg')));

        if ($upload->isValid()) {
            $upload->receive();

            do {
                $filename = sha1(uniqid()) . '.jpg';
            } while(file_exists($filePath . $filename));

            $image = new Imagick($upload->getFileName());

            $exif = exif_read_data($upload->getFileName());
            unlink($upload->getFileName());

            if (isset($exif['Orientation'])) {
                switch($exif['Orientation']) {
                    case 1: // nothing
                        break;
                    case 2: // horizontal flip
                        $image->flopImage();
                        break;
                    case 3: // 180 rotate
                        $image->rotateImage(new ImagickPixel(), 180);
                        break;
                    case 4: // vertical flip
                        $image->flipImage();
                        break;
                    case 5: // vertical flip + 90 rotate clockwise
                        $image->flipImage();
                        $image->rotateImage(new ImagickPixel(), 90);
                        break;
                    case 6: // 90 rotate clockwise
                        $image->rotateImage(new ImagickPixel(), 90);
                        break;
                    case 7: // horizontal flip + 90 rotate clockwise
                        $image->flopImage();
                        $image->rotateImage(new ImagickPixel(), 90);
                        break;
                    case 8:    // 90 rotate counter clockwise
                        $image->rotateImage(new ImagickPixel(), -90);
                        break;
                }
            }

            $image->scaleImage(640, 480, true);
            $thumb = clone $image;
            $watermark = new Imagick();
            $watermark->readImage(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('gallery.watermark_path')
            );
            $watermark->scaleImage(57, 48);
            $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, 0, $image->getImageHeight() - 50);
            $image->writeImage($filePath . $filename);

            $thumb->cropThumbnailImage(150, 150);
            $image->writeImage($filePath . 'thumbs/'. $filename);

            $photo = new Photo($album, $filename);
            $this->getEntityManager()->persist($photo);
            $this->getEntityManager()->flush();

            return new ViewModel(
                array(
                    'result' => array(
                        'status' => 'success'
                    ),
                )
            );
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'error'
                ),
            )
        );
    }

    public function censorPhotoAction()
    {
        if (!($photo = $this->_getPhoto()))
            return new ViewModel();

        $photo->setCensored(true);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Succes',
                'The photo was successfully censored!'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function unCensorPhotoAction()
    {
        if (!($photo = $this->_getPhoto()))
            return new ViewModel();

        $photo->setCensored(false);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Succes',
                'The photo was successfully uncensored!'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function _getAlbum()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the album!'
                )
            );

            $this->redirect()->toRoute(
                'gallery_admin_gallery',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $album = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Album')
            ->findOneById($this->getParam('id'));

        if (null === $album) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No album with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'gallery_admin_gallery',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $album;
    }

    public function _getPhoto()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the photo!'
                )
            );

            $this->redirect()->toRoute(
                'gallery_admin_gallery',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $album = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Photo')
            ->findOneById($this->getParam('id'));

        if (null === $album) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No photo with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'gallery_admin_gallery',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $album;
    }
}
