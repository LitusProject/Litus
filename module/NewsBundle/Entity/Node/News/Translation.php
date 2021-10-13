<?php

namespace NewsBundle\Entity\Node\News;

use CommonBundle\Component\Util\StringUtil;
use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use NewsBundle\Entity\Node\News;
use Parsedown;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NewsBundle\Repository\Node\News\Translation")
 * @ORM\Table(name="nodes_news_translations")
 */
class Translation
{
    /**
     * @var integer The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var News The news of this translation
     *
     * @ORM\ManyToOne(targetEntity="NewsBundle\Entity\Node\News", inversedBy="translations")
     * @ORM\JoinColumn(name="news", referencedColumnName="id")
     */
    private $news;

    /**
     * @var Language The language of this translation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The title of this translation
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string The content of this translation
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @param News     $news
     * @param Language $language
     * @param string   $title
     * @param string   $content
     */
    public function __construct(News $news, Language $language, $title, $content)
    {
        $this->news = $news;
        $this->language = $language;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return News
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getSummary($length = 100)
    {
        $parsedown = new Parsedown();
        $summary = $parsedown->text($this->content);

        return StringUtil::truncate($summary, $length, '...');
    }

    /**
     * @param string $content
     * @param self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
