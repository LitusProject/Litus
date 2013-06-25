<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace NewsBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    Doctrine\ORM\Mapping as ORM,
    Markdown_Parser;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NewsBundle\Repository\Nodes\Translation")
 * @ORM\Table(name="nodes.news_translations")
 */
class Translation
{
    /**
     * @var int The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \NewsBundle\Entity\Nodes\News The news of this translation
     *
     * @ORM\ManyToOne(targetEntity="NewsBundle\Entity\Nodes\News", inversedBy="translations")
     * @ORM\JoinColumn(name="news", referencedColumnName="id")
     */
    private $news;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this translation
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
     * @param \NewsBundle\Entity\Nodes\News $news
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $title
     * @param string $content
     */
    public function __construct(News $news, Language $language, $title, $content)
    {
        $this->news = $news;
        $this->language = $language;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @return \NewsBundle\Entity\Nodes\News
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @return \CommonBundle\Entity\General\Language
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
     * @param string $title
     *
     * @return \NewsBundle\Entity\Nodes\Translation
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
        $parser = new Markdown_Parser();
        $summary = $parser->transform($this->content);
        return \CommonBundle\Component\Util\String::truncate($summary, $length, '...');
    }

    /**
     * @param string $content
     *
     * @param \NewsBundle\Entity\Nodes\Translation
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
