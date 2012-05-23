<?php

namespace GalleryBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    GalleryBundle\Entity\Album\Album,
    GalleryBundle\Entity\Album\Translation,
    GalleryBundle\Entity\Album\Photo,
    GalleryBundle\Form\Admin\Album\Add as AddForm,
    GalleryBundle\Form\Admin\Album\Edit as EditForm,
    Imagick,
    Zend\Http\Headers,
    Zend\File\Transfer\Transfer as FileTransfer;

/**
 * Handles system admin for gallery.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GalleryController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $albums = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Album')
            ->findAll();
        
        return array(
        	'albums' => $albums,
        );
    }
    
    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
            if ($form->isValid($formData)) {
                $album = new Album($this->getAuthentication()->getPersonObject(), \DateTime::createFromFormat('d/m/Y', $formData['date']));
                $this->getEntityManager()->persist($album);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();
                
                foreach($languages as $language) {
                    $translation = new Translation($album, $language, $formData['title_' . $language->getAbbrev()]);
                    $this->getEntityManager()->persist($translation);
                    
                    if ($language->getAbbrev() == 'en')
                        $title = $formData['title_' . $language->getAbbrev()];
                }

                $this->getEntityManager()->flush();
                
                \CommonBundle\Component\Log\Log::createLog(
                    $this->getEntityManager(),
                    'action',
                    $this->getAuthentication()->getPersonObject(),
                    'Gallery album added: ' . $title
                );
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The album was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_gallery',
                	array(
                		'action' => 'addPhotos',
                		'id' => $album->getId(),
                	)
                );
                
                return;
            }
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function editAction()
    {
        if (!($album = $this->_getAlbum()))
            return;
        
        $form = new EditForm($this->getEntityManager(), $album);
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
        	
            if ($form->isValid($formData)) {
                $album->setDate(\DateTime::createFromFormat('d/m/Y', $formData['date']));

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
                
                \CommonBundle\Component\Log\Log::createLog(
                    $this->getEntityManager(),
                    'action',
                    $this->getAuthentication()->getPersonObject(),
                    'Gallery album edited: ' . $title
                );
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The album was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                	'admin_gallery',
                	array(
                		'action' => 'manage'
                	)
                );
                
                return;
            }
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function deleteAction()
    {
        $this->initAjax();
        
        if (!($album = $this->_getAlbum()))
            return;
        
        $this->getEntityManager()->remove($album);
        
        $this->getEntityManager()->flush();
        
        \CommonBundle\Component\Log\Log::createLog(
            $this->getEntityManager(),
            'action',
            $this->getAuthentication()->getPersonObject(),
            'Gallery album deleted: ' . $album->getTitle(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en')
                )
        );
    	
    	return array(
    		'result' => array(
    			'status' => 'success'
    		),
    	);
    }
    
    public function photosAction()
    {
        if (!($album = $this->_getAlbum()))
            return;
            
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery_path');
            
        return array(
            'album' => $album,
            'filePath' => $filePath,
        );
    }
    
    public function addPhotosAction()
    {
        if (!($album = $this->_getAlbum()))
            return;

        return array(
            'album' => $album,
        );
    }
    
    public function uploadAction()
    {
        if (!($album = $this->_getAlbum()))
            return;
            
        $filePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery_path');

        if (!file_exists($filePath . '/' . $album->getId() . '/thumbs/')) {
            if (!file_exists($filePath . '/' . $album->getId() . '/'))
                mkdir($filePath . '/' . $album->getId() . '/');
            mkdir($filePath . '/' . $album->getId() . '/thumbs/');
        }
        
        $file = new FileTransfer();
        $file->receive();
        
        do {
            $filename = '/' . sha1(uniqid());
        } while(file_exists($filePath . '/' . $album->getId() . $filename));
        
        $image = new Imagick($file->getFileName());
        
        $exif = exif_read_data($file->getFileName());
        
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
        
        $image->scaleImage(640, 480, true);
        $image->writeImage($filePath . '/' . $album->getId() . $filename);
        $image->cropThumbnailImage(150, 150);
        $image->writeImage($filePath . '/' . $album->getId() . '/thumbs'. $filename);
        
        $photo = new Photo($album, $filename);
        $this->getEntityManager()->persist($photo);
        $this->getEntityManager()->flush();
        
        return array(
        	'result' => array(
        		'status' => 'success'
        	),
        );
    }
    
    public function censorPhotoAction()
    {
        if (!($photo = $this->_getPhoto()))
            return;
            
        $photo->setCensored(true);
        $this->getEntityManager()->flush();
        
        \CommonBundle\Component\Log\Log::createLog(
            $this->getEntityManager(),
            'action',
            $this->getAuthentication()->getPersonObject(),
            'Photo censored in album: ' . $photo->getAlbum()->getTitle(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en')
                ) . ' with id ' . $photo->getId()
        );
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Succes',
                'The photo was successfully censored!'
            )
        );
        
        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
        
        return;
    }
    
    public function UnCensorPhotoAction()
    {
        if (!($photo = $this->_getPhoto()))
            return;
            
        $photo->setCensored(false);
        $this->getEntityManager()->flush();
        
        \CommonBundle\Component\Log\Log::createLog(
            $this->getEntityManager(),
            'action',
            $this->getAuthentication()->getPersonObject(),
            'Photo uncensored in album: ' . $photo->getAlbum()->getTitle(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en')
                ) . ' with id ' . $photo->getId()
        );
        
        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Succes',
                'The photo was successfully uncensored!'
            )
        );
        
        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
        
        return;
    }
    
    public function viewPhotoAction()
    {
        if (!($photo = $this->_getPhoto()))
            return;
        
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery_path');
        
        $path = $filePath . '/' . $photo->getAlbum()->getId() . $photo->getFilePath();
        
        $headers = new Headers();
        $headers->addHeaders(array(
        	'Content-type' => mime_content_type($path),
        ));
        $this->getResponse()->setHeaders($headers);
        
        $handle = fopen($path, 'r');
        $data = fread($handle, filesize($path));
        fclose($handle);
        
        return array(
            'data' => $data,
        );
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
    			'admin_gallery',
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
    			'admin_gallery',
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
    			'admin_gallery',
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
    			'admin_gallery',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $album;
    }
}