<?php

namespace GalleryBundle\Controller;

use CommonBundle\Component\Util\AcademicYear,
    Zend\Http\Headers;

/**
 * Handles system gallery controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GalleryController extends \CommonBundle\Component\Controller\ActionController\CommonController
{
    public function overviewAction()
    {
        $albums = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Album')
            ->findAll();
        
        $sorted = array();
        foreach($albums as $album) {
            $year = AcademicYear::getAcademicYear($album->getDate());
            if (!isset($sorted[$year])) {
                $sorted[$year] = (object) array(
                    'year' => $year,
                    'albums' => array(),
                );
            }
            $sorted[$year]->albums[] = $album;
        }
        
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery_path');

        return array(
            'albums' => $sorted,
            'currentYear' => AcademicYear::getAcademicYear(),
            'filePath' => $filePath,
        );
    }
    
    public function yearAction()
    {
        $start = AcademicYear::getDateTime($this->getParam('id'));
        $end = clone $start;
        $end = AcademicYear::getStartOfAcademicYear($end->modify('+1year +2months'));

        $albums = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Album')
            ->findAllFromTo($start, $end);
            
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery_path');
            
        return array(
            'albums' => $albums,
            'filePath' => $filePath,
            'academicYear' => AcademicYear::getAcademicYear($end),
        );
    }

    public function albumAction()
    {
        if (!($album = $this->_getTranslationByName()))
            return;
            
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery_path');

        return array(
            'album' => $album->getAlbum(),
            'filePath' => $filePath,
        );
    }
    
    public function _getTranslationByName()
    {
        if (null === $this->getParam('id')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    
        $translation = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Translation')
            ->findOneByName($this->getParam('id'));
        
        if (null === $translation || $translation->getLanguage() != $this->getLanguage()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        return $translation;
    }
    
    public function _getPhoto()
    {
        if (null === $this->getParam('id')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    
        $album = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Photo')
            ->findOneById($this->getParam('id'));
        
        if (null === $album) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        return $album;
    }
}
