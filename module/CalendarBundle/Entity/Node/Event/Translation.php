<?php

namespace CalendarBundle\Entity\Node\Event;

use CalendarBundle\Entity\Node\Event;
use CommonBundle\Component\Util\StringUtil;
use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use Parsedown;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CalendarBundle\Repository\Node\Event\Translation")
 * @ORM\Table(name="nodes_events_translations")
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
     * @var Event The event of this translation
     *
     * @ORM\ManyToOne(targetEntity="CalendarBundle\Entity\Node\Event", inversedBy="translations")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var Language The language of this tanslation
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
     * @param Event    $event
     * @param Language $language
     * @param string   $location
     * @param string   $title
     * @param string   $content
     */
    public function __construct(Event $event, Language $language, $location, $title, $content)
    {
        $this->event = $event;
        $this->language = $language;
        $this->location = $location;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return self
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
     * @param string $content
     *
     * @return self
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
        $parsedown = new Parsedown();
        $summary = $parsedown->text($this->content);

        return StringUtil::truncateNoHtml($summary, $length, '...');
    }
}
