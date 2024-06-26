<?php

namespace ApiBundle\Controller;

use DateInterval;
use DateTime;
use Laminas\View\Model\ViewModel;
use Parsedown;

/**
 * NewsController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class NewsController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function allAction()
    {
        $this->initJson();

        $maxAge = new DateTime();
        $maxAge->sub(
            new DateInterval(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('news.max_age_site')
            )
        );

        $items = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findApi($maxAge);

        $summaryLength = $this->getRequest()->getPost('summary_length');
        if (!is_numeric($summaryLength) || $summaryLength <= 0) {
            $summaryLength = 200;
        }

        $result = array();
        foreach ($items as $item) {
            $parsedown = new Parsedown();
            $summary = preg_replace('/\s+/', ' ', strip_tags($parsedown->text($item->getContent($this->getLanguage()))));
            $summary = substr($summary, 0, $summaryLength);

            $result[] = array(
                'id'           => $item->getId(),
                'creationTime' => $item->getCreationTime()->format('c'),
                'endDate'      => $item->getEndDate() ? $item->getEndDate()->format('c') : null,
                'message'      => trim(strip_tags(str_replace(array('<br />', '<br>'), "\r\n", $item->getContent($this->getLanguage())))),
                'summary'      => $summary,
                'title'        => $item->getTitle($this->getLanguage()),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }
}
