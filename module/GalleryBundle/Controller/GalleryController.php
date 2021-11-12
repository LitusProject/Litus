<?php

namespace GalleryBundle\Controller;

use CommonBundle\Component\Util\AcademicYear;
use DateInterval;
use GalleryBundle\Entity\Album;
use GalleryBundle\Entity\Album\Photo;
use Laminas\View\Model\ViewModel;

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
            ->getRepository('GalleryBundle\Entity\Album')
            ->findAll();

        $sorted = array();
        foreach ($albums as $album) {
            if (count($album->getPhotos()) == 0) {
                continue;
            }

            $date = $album->getDate();
            $date->add(new DateInterval('P1W'));
            $year = AcademicYear::getAcademicYear($date);
            if (!isset($sorted[$year])) {
                $sorted[$year] = (object) array(
                    'year'   => $year,
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
                'albums'      => $sorted,
                'currentYear' => AcademicYear::getAcademicYear(),
                'filePath'    => $filePath,
                'archiveUrl'  => $archiveUrl,
            )
        );
    }

    public function yearAction()
    {
        $start = AcademicYear::getDateTime($this->getParam('name'));
        $end = clone $start;
        $end = AcademicYear::getStartOfAcademicYear($end->modify('+1year +2months'));

        $start->sub(new DateInterval('P1W'));
        $end->sub(new DateInterval('P1W'));

        $albumsFound = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album')
            ->findAllFromTo($start, $end);

        $albums = array();
        foreach ($albumsFound as $album) {
            if (count($album->getPhotos()) >= 0) {
                $albums[] = $album;
            }
        }

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path');

        return new ViewModel(
            array(
                'albums'       => $albums,
                'filePath'     => $filePath,
                'academicYear' => AcademicYear::getAcademicYear($end),
            )
        );
    }

    public function albumAction()
    {
        $album = $this->getAlbumEntity();
        if ($album === null) {
            return $this->notFoundAction();
        }

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path');

        $allowCensor = false;
        if ($this->getAuthentication()->isAuthenticated()) {
            if ($this->getAuthentication()->getPersonObject()->isPraesidium($this->getCurrentAcademicYear())
                && $this->hasAccess()->toResourceAction('gallery', 'censor') && $this->hasAccess()->toResourceAction('gallery', 'uncensor')
            ) {
                $allowCensor = true;
            }
        }

        return new ViewModel(
            array(
                'album'       => $album,
                'filePath'    => $filePath,
                'allowCensor' => $allowCensor,
            )
        );
    }

    public function censorAction()
    {
        if (!$this->getAuthentication()->isAuthenticated() || !$this->getAuthentication()->getPersonObject()->isPraesidium($this->getCurrentAcademicYear())) {
            return $this->notFoundAction();
        }

        $photo = $this->getPhotoEntity();
        if ($photo === null) {
            return $this->notFoundAction();
        }

        $photo->setCensored(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function uncensorAction()
    {
        if (!$this->getAuthentication()->isAuthenticated() || !$this->getAuthentication()->getPersonObject()->isPraesidium($this->getCurrentAcademicYear())) {
            return $this->notFoundAction();
        }

        $photo = $this->getPhotoEntity();
        if ($photo === null) {
            return $this->notFoundAction();
        }

        $photo->setCensored(false);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Album|null
     */
    private function getAlbumEntity()
    {
        $album = $this->getEntityById('GalleryBundle\Entity\Album', 'name', 'name');

        if (!($album instanceof Album)) {
            return;
        }

        return $album;
    }

    /**
     * @return Photo|null
     */
    private function getPhotoEntity()
    {
        $photo = $this->getEntityById('GalleryBundle\Entity\Album\Photo', 'id', 'name');

        if (!($photo instanceof Photo)) {
            return;
        }

        return $photo;
    }
}
