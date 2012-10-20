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

        $archiveUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.archive_url');

        return new ViewModel(
            array(
                'albums' => $sorted,
                'currentYear' => AcademicYear::getAcademicYear(),
                'filePath' => $filePath,
                'archiveUrl' => $archiveUrl,
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

        $allowCensor = false;
        if ($this->getAuthentication()->isAuthenticated()) {
            if ($this->getAuthentication()->getPersonObject()->isPraesidium($this->getCurrentAcademicYear())
                && $this->hasAccess('gallery', 'censor') && $this->hasAccess('gallery', 'uncensor'))
                $allowCensor = true;
        }

        return new ViewModel(
            array(
                'album' => $album,
                'filePath' => $filePath,
                'allowCensor' => $allowCensor,
            )
        );
    }

    public function censorAction()
    {
        if (!$this->getAuthentication()->isAuthenticated() || !$this->getAuthentication()->getPersonObject()->isPraesidium($this->getCurrentAcademicYear())) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if (!($photo = $this->_getPhoto()))
            return new ViewModel();

        $photo->setCensored(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function uncensorAction()
    {
        if (!$this->getAuthentication()->isAuthenticated() || !$this->getAuthentication()->getPersonObject()->isPraesidium($this->getCurrentAcademicYear())) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if (!($photo = $this->_getPhoto()))
            return new ViewModel();

        $photo->setCensored(false);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function _getAlbumByName()
    {
    	if (null === $this->getParam('name')) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}

        $album = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Album')
            ->findOneByName($this->getParam('name'));

    	if (null === $album) {
    	    $this->getResponse()->setStatusCode(404);
    		return;
    	}

    	return $album;
    }

    public function _getPhoto()
    {
    	if (null === $this->getParam('name')) {
    		$this->getResponse()->setStatusCode(404);
    		return;
    	}

        $album = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Photo')
            ->findOneById($this->getParam('name'));

    	if (null === $album) {
    		$this->getResponse()->setStatusCode(404);
    		return;
    	}

    	return $album;
    }
}
