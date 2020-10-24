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

namespace NewsBundle\Component\Document\Generator;

use CommonBundle\Component\Controller\Plugin\Url;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Node as XmlNode;
use CommonBundle\Entity\General\Language;
use Doctrine\ORM\EntityManager;
use Locale;
use NewsBundle\Entity\Node\News;
use Laminas\Http\PhpEnvironment\Request;

/**
 * Feed
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Feed extends \CommonBundle\Component\Util\Xml\Generator
{
    /**
     * @var EntityManager The EntityManager
     */
    private $entityManager;

    /**
     * @var string The base url
     */
    private $serverName;

    /**
     * @var Language The language
     */
    private $language;

    /**
     * @var Url The url plugin
     */
    private $url;

    /**
     * @param TmpFile       $file
     * @param EntityManager $entityManager
     * @param Language      $language
     * @param Request       $request
     * @param Url           $url
     */
    public function __construct(TmpFile $file, EntityManager $entityManager, Language $language, Request $request, Url $url)
    {
        parent::__construct($file);

        $this->entityManager = $entityManager;
        $this->serverName = 'http://' . str_replace(',', '', $request->getServer('SERVER_NAME'));
        $this->language = $language;
        $this->url = $url;

        $feed = new XmlNode(
            'rss',
            array(
                'version' => '2.0',
            ),
            array(
                new XmlNode(
                    'channel',
                    array(),
                    $this->generateContent()
                ),
            )
        );

        $this->append($feed);
    }

    /**
     * @return XmlNode[]
     */
    private function generateContent()
    {
        $data = $this->generateHeader();

        $news = $this->entityManager
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findAllSite();

        foreach ($news as $item) {
            $data[] = $this->generateItemXml($item);
        }

        return $data;
    }

    /**
     * @return XmlNode[]
     */
    private function generateHeader()
    {
        $config = $this->entityManager->getRepository('CommonBundle\Entity\General\Config');

        $descriptions = unserialize($config->getConfigValue('news.rss_description'));
        if (isset($descriptions[$this->language->getAbbrev()])) {
            $description = $descriptions[$this->language->getAbbrev()];
        } else {
            $description = $descriptions[Locale::getDefault()];
        }

        return array(
            new XmlNode(
                'title',
                array(),
                $config->getConfigValue('news.rss_title')
            ),
            new XmlNode(
                'description',
                array(),
                $description
            ),
            new XmlNode(
                'language',
                array(),
                $this->language->getAbbrev()
            ),
            new XmlNode(
                'link',
                array(),
                $this->serverName . $this->url->fromRoute(
                    'news',
                    array(
                        'feed',
                    )
                )
            ),
            new XmlNode(
                'image',
                array(),
                array(
                    new XmlNode(
                        'title',
                        array(),
                        $config->getConfigValue('news.rss_title')
                    ),
                    new XmlNode(
                        'url',
                        array(),
                        $this->serverName . $config->getConfigValue('news.rss_image_link')
                    ),
                    new XmlNode(
                        'link',
                        array(),
                        $this->serverName . $this->url->fromRoute(
                            'news',
                            array(
                                'feed',
                            )
                        )
                    ),
                )
            ),
        );
    }

    /**
     * @param  News $item
     * @return XmlNode
     */
    private function generateItemXml(News $item)
    {
        return new XmlNode(
            'item',
            array(),
            array(
                new XmlNode(
                    'title',
                    array(),
                    $item->getTitle($this->language)
                ),
                new XmlNode(
                    'description',
                    array(),
                    $item->getSummary(200, $this->language)
                ),
                new XmlNode(
                    'link',
                    array(),
                    $this->serverName . $this->url->fromRoute(
                        'news',
                        array(
                            'action' => 'view',
                            'name'   => $item->getName(),
                        )
                    )
                ),
                new XmlNode(
                    'guid',
                    array(),
                    $this->serverName . $this->url->fromRoute(
                        'news',
                        array(
                            'action' => 'view',
                            'name'   => $item->getName(),
                        )
                    )
                ),
                new XmlNode(
                    'pubDate',
                    array(),
                    $item->getCreationTime()->format('D, d M Y H:i:s O')
                ),
            )
        );
    }
}
