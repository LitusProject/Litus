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

namespace GalleryBundle\Controller;

use CommonBundle\Component\Util\AcademicYear,
    DateInterval,
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
        foreach ($albums as $album) {
            $date = $album->getDate();
            $date->add(new DateInterval('P1W'));
            $year = AcademicYear::getAcademicYear($date);
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
        $start = AcademicYear::getDateTime($this->getParam('name'));
        $end = clone $start;
        $end = AcademicYear::getStartOfAcademicYear($end->modify('+1year +2months'));

        $start->sub(new DateInterval('P1W'));
        $end->sub(new DateInterval('P1W'));

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
            return $this->notFoundAction();

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
            return $this->notFoundAction();
        }

        if (!($photo = $this->_getPhoto()))
            return $this->notFoundAction();

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
            return $this->notFoundAction();
        }

        if (!($photo = $this->_getPhoto()))
            return $this->notFoundAction();

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
            return;
        }

        $album = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Album')
            ->findOneByName($this->getParam('name'));

        if (null === $album) {
            return;
        }

        return $album;
    }

    public function _getPhoto()
    {
        if (null === $this->getParam('name')) {
            return;
        }

        $album = $this->getEntityManager()
            ->getRepository('GalleryBundle\Entity\Album\Photo')
            ->findOneById($this->getParam('name'));

        if (null === $album) {
            return;
        }

        return $album;
    }
}
