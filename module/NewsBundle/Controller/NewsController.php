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

use CommonBundle\Component\Util\Xml\Object as XmlObject,
    DateTime,
    IntlDateFormatter,
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
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => 'application/rss+xml',
        ));
        $this->getResponse()->setHeaders($headers);

        $config = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config');

        $description = '';
        $descriptions = unserialize($config->getConfigValue('news.rss_description'));
        if (isset($descriptions[$this->getLanguage()->getAbbrev()]))
            $description = $descriptions[$this->getLanguage()->getAbbrev()];
        else
            $description = $descriptions[\Locale::getDefault()];

        $serverName = 'http://' . str_replace(',', '', $this->getRequest()->getServer('SERVER_NAME'));

        $data = array(
            new XmlObject(
                'title',
                array(),
                $config->getConfigValue('news.rss_title')
            ),
            new XmlObject(
                'description',
                array(),
                $description
            ),
            new XmlObject(
                'language',
                array(),
                $this->getLanguage()->getAbbrev()
            ),
            new XmlObject(
                'link',
                array(),
                $serverName . $this->url()->fromRoute(
                    'news',
                    array(
                        'feed',
                    )
                )
            ),
            new XmlObject(
                'image',
                array(),
                array(
                    new XmlObject(
                        'title',
                        array(),
                        $config->getConfigValue('news.rss_title')
                    ),
                    new XmlObject(
                        'url',
                        array(),
                        $serverName . $config->getConfigValue('news.rss_image_link')
                    ),
                    new XmlObject(
                        'link',
                        array(),
                        $serverName . $this->url()->fromRoute(
                            'news',
                            array(
                                'feed',
                            )
                        )
                    ),
                )
            ),
        );

        $news = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findAllSite();

        foreach ($news as $item) {
            $data[] = new XmlObject(
                'item',
                array(),
                array(
                    new XmlObject(
                        'title',
                        array(),
                        $item->getTitle($this->getLanguage())
                    ),
                    new XmlObject(
                        'description',
                        array(),
                        $item->getSummary(200, $this->getLanguage())
                    ),
                    new XmlObject(
                        'link',
                        array(),
                        $serverName . $this->url()->fromRoute(
                            'news',
                            array(
                                'action' => 'view',
                                'name' => $item->getName($this->getLanguage()),
                            )
                        )
                    ),
                    new XmlObject(
                        'guid',
                        array(),
                        $serverName . $this->url()->fromRoute(
                            'news',
                            array(
                                'action' => 'view',
                                'name' => $item->getName($this->getLanguage()),
                            )
                        )
                    ),
                    new XmlObject(
                        'pubDate',
                        array(),
                        $item->getCreationTime()->format('D, d M Y H:i:s O')
                    ),
                )
            );
        }

        $feed = new XmlObject(
            'rss',
            array(
                'version' => '2.0',
            ),
            array(
                new XmlObject(
                    'channel',
                    array(),
                    $data
                ),
            )
        );

        return new ViewModel(
            array(
                'header' => '<?xml version="1.0" encoding="ISO-8859-1"?>',
                'feed' => $feed,
            )
        );
    }

    public function _getNews()
    {
        if (null === $this->getParam('name'))
            return;

        $news = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findOneByName($this->getParam('name'));

        if (null === $news)
            return;

        return $news;
    }
}
