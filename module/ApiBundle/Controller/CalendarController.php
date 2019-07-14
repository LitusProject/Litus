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

namespace ApiBundle\Controller;

use CommonBundle\Component\Util\File\TmpFile;
use Imagick;
use Zend\View\Model\ViewModel;

/**
 * CalendarController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Marïen <kristof.marien@litus.cc>
 */
class CalendarController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function activeEventsAction()
    {
        $this->initJson();

        $items = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $result = array();
        foreach ($items as $item) {
            $result[] = array(
                'id'        => $item->getId(),
                'title'     => $item->getTitle($this->getLanguage()),
                'content'   => trim(strip_tags(str_replace(array('<br />', '<br>'), "\r\n", $item->getContent($this->getLanguage())))),
                'startDate' => $item->getStartDate()->format('c'),
                'endDate'   => $item->getEndDate() ? $item->getEndDate()->format('c') : null,
                'poster'    => $item->getPoster(),
                'location'  => $item->getLocation(),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }

    public function posterAction()
    {
        $this->initJson();

        $poster = $this->getPoster();
        if ($poster === null) {
            return $this->error(404, 'No poster key was provided with the request');
        }

        $path = $this->getStoragePath(
            'calendar_events_posters',
            $poster
        );

        if (!$this->getFilesystem()->has($path)) {
            return $this->error(404, 'The requested poster does not exist');
        }

        $result = array(
            'mime_type' => $this->getFilesystem()->getMimetype($path),
            'data'      => base64_encode($this->getFilesystem()->read($path)),
        );

        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }

    /**
     * @return string|null
     */
    private function getPoster()
    {
        return $this->getRequest()->getQuery('poster');
    }
}
