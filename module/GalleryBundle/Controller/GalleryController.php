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

namespace GalleryBundle\Controller;

use CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    GalleryBundle\Entity\Album\Album,
    GalleryBundle\Entity\Album\Photo,
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
        foreach ($albums as $album) {
            if (sizeof($album->getPhotos()) == 0) {
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
            ->getRepository('GalleryBundle\Entity\Album\Album')
            ->findAllFromTo($start, $end);

        $albums = array();
        foreach ($albumsFound as $album) {
            if (sizeof($album->getPhotos()) >= 0) {
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
        if (!($album = $this->getAlbumEntity())) {
            return $this->notFoundAction();
        }

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('gallery.path');

        $allowCensor = false;
        if ($this->getAuthentication()->isAuthenticated()) {
            if ($this->getAuthentication()->getPersonObject()->isPraesidium($this->getCurrentAcademicYear())
                && $this->hasAccess()->toResourceAction('gallery', 'censor') && $this->hasAccess()->toResourceAction('gallery', 'uncensor')) {
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

        if (!($photo = $this->getPhotoEntity())) {
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

        if (!($photo = $this->getPhotoEntity())) {
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
        $album = $this->getEntityById('GalleryBundle\Entity\Album\Album', 'name', 'name');

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
