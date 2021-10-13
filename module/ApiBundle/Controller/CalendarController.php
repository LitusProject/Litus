<?php

namespace ApiBundle\Controller;

use Imagick;
use Laminas\View\Model\ViewModel;

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

        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('calendar.poster_path') . '/';

        if (!file_exists($filePath . $poster)) {
            return $this->error(404, 'The poster file does not exist');
        }

        if (!file_exists($filePath . $poster . '_thumb')) {
            $image = new Imagick($filePath . $poster);
            $image->scaleImage(1136, 1136, true);
            $image->writeImage($filePath . $poster . '_thumb');
        }

        $handle = fopen($filePath . $poster . '_thumb', 'r');
        $data = base64_encode(fread($handle, filesize($filePath . $poster . '_thumb')));
        fclose($handle);

        $result = array(
            'mime_type' => mime_content_type($filePath . $poster),
            'data'      => $data,
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
        $poster = $this->getRequest()->getQuery('poster');
        if (is_string($poster)) {
            return $poster;
        }

        return null;
    }
}
