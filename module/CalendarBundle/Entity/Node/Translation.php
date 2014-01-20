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

namespace CalendarBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM,
    Markdown_Parser;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CalendarBundle\Repository\Node\Translation")
 * @ORM\Table(name="nodes.events_translations")
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
     * @var \CalendarBundle\Entity\Node\Event The event of this translation
     *
     * @ORM\ManyToOne(targetEntity="CalendarBundle\Entity\Node\Event", inversedBy="translations")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The location of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $location;

    /**
     * @var string The title of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string The title of this tanslation
     *
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @param \CalendarBundle\Entity\Node\Event $event
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $location
     * @param string $title
     * @param string $content
     */
    public function __construct(Event $event, Language $language, $location, $title, $content)
    {
        $this->event= $event;
        $this->language = $language;
        $this->location = $location;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @return \CalendarBundle\Entity\Node\Event
     */
    public function getEvent()
    {
        return $this->event;
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return \CalendarBundle\Entity\Node\Translation
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
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
     * @return \CalendarBundle\Entity\Node\Translation
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
     * @param string $content
     *
     * @return \CalendarBundle\Entity\Node\Translation
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary($length = 100)
    {
        $parser = new Markdown_Parser();
        $summary = $parser->transform($this->content);

        return \CommonBundle\Component\Util\String::truncateNoHtml($summary, $length, '...');
    }
}
