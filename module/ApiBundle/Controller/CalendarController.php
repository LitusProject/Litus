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

namespace ApiBundle\Controller;

use Imagick,
    Zend\View\Model\ViewModel;

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
                'id' => $item->getId(),
                'title' => $item->getTitle($this->getLanguage()),
                'content' => trim(strip_tags(str_replace(array('<br />', '<br>'), "\r\n", $item->getContent($this->getLanguage())))),
                'startDate' => $item->getStartDate()->format('c'),
                'endDate' => $item->getEndDate() ? $item->getEndDate()->format('c') : null,
                'poster' => $item->getPoster(),
                'location' => $item->getLocation()
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    public function posterAction()
    {
        if (null === ($poster = $this->_getPoster()))
            return $this->error(404, 'No poster key was provided with the request');

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.poster_path') . '/';

        if (!file_exists($filePath . $poster))
            return $this->error(500, 'The poster file does not exist');

        if (!file_exists($filePath . $poster . '_thumb')) {
            $image = new Imagick($filePath . $poster);
            $image->scaleImage(1136, 1136, true);
            $image->writeImage($filePath . $poster. '_thumb');
        }

        $handle = fopen($filePath . $poster . '_thumb', 'r');
        $data = base64_encode(fread($handle, filesize($filePath . $poster . '_thumb')));
        fclose($handle);

        $result = array(
            'mime_type' => mime_content_type($filePath . $poster),
            'data' => $data
        );

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    private function _getPoster()
    {
        if (null !== $this->getRequest()->getPost('poster'))
            return $this->getRequest()->getPost('poster');

        return null;
    }
}
