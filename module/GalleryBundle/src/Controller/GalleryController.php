<?php

namespace GalleryBundle\Controller;

use CommonBundle\Component\Util\AcademicYear,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * GalleryController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GalleryController extends \CommonBundle\Component\Controller\ActionController\SiteController
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
            ->getConfigValue('gallery.path');

        return new ViewModel(
            array(
                'albums' => $sorted,
                'currentYear' => AcademicYear::getAcademicYear(),
                'filePath' => $filePath,
            )
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
            ->getConfigValue('gallery.path');

        return new ViewModel(
            array(
                'albums' => $albums,
                'filePath' => $filePath,
                'academicYear' => AcademicYear::getAcademicYear($end),
            )
        );
    }

    public function albumAction()
    {
        if (!($album = $this->_getAlbumByName()))
            return new ViewModel();

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path');

        return new ViewModel(
            array(
                'album' => $album,
                'filePath' => $filePath,
            )
        );
    }

    public function _getAlbumByName()
    {
    	if (null === $this->getParam('id')) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}

        $album = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Album')
            ->findOneByName($this->getParam('id'));

    	if (null === $album) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}

    	return $album;
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
