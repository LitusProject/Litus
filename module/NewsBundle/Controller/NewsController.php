<?php

namespace NewsBundle\Controller;

use CommonBundle\Component\Util\File\TmpFile;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use NewsBundle\Component\Document\Generator\Feed as FeedGenerator;
use NewsBundle\Entity\Node\News;

/**
 * NewsController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NewsController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('NewsBundle\Entity\Node\News')
                ->findAllSiteQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function viewAction()
    {
        $news = $this->getNewsEntity();
        if ($news === null) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            array(
                'news' => $news,
            )
        );
    }

    public function feedAction()
    {
        $feedFile = new TmpFile();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Type' => 'application/rss+xml',
            )
        );
        $this->getResponse()->setHeaders($headers);

        new FeedGenerator($feedFile, $this->getEntityManager(), $this->getLanguage(), $this->getRequest(), $this->url());

        return new ViewModel(
            array(
                'result' => $feedFile->getContent(),
            )
        );
    }

    /**
     * @return News|null
     */
    private function getNewsEntity()
    {
        $news = $this->getEntityById('NewsBundle\Entity\Node\News', 'name', 'name');

        if (!($news instanceof News)) {
            return;
        }

        return $news;
    }
}
