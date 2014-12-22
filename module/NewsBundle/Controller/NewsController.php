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

namespace NewsBundle\Controller;




use CommonBundle\Component\Util\File\TmpFile as TmpFile,
    NewsBundle\Component\Document\Generator\Feed as FeedGenerator,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

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
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function viewAction()
    {
        if (!($news = $this->_getNews())) {
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
        $headers->addHeaders(array(
            'Content-Type' => 'application/rss+xml',
        ));
        $this->getResponse()->setHeaders($headers);

        new FeedGenerator($feedFile, $this->getEntityManager(), $this->getLanguage(), $this->getRequest(), $this->url());

        return new ViewModel(
            array(
                'result' => $feedFile->getContent(),
            )
        );
    }

    public function _getNews()
    {
        if (null === $this->getParam('name')) {
            return;
        }

        $news = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findOneByName($this->getParam('name'));

        if (null === $news) {
            return;
        }

        return $news;
    }
}
