<?php
 
namespace CalendarBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="CalendarBundle\Repository\Nodes\Translation")
 * @Table(name="nodes.event_translation")
 */
class Translation
{
    /**
     * @var int The ID of this tanslation
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @var \CalendarBundle\Entity\Nodes\Event The event of this translation
     *
     * @ManyToOne(targetEntity="CalendarBundle\Entity\Nodes\Event", inversedBy="translations")
     * @JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;
        
    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;
    
    /**
     * @var string The location of this tanslation
     *
     * @Column(type="string")
     */
    private $location;
      
    /**
     * @var string The title of this tanslation
     *
     * @Column(type="string")
     */
    private $title;
    
    /**
     * @var string The name of this tanslation
     *
     * @Column(type="string")
     */
    private $name;
    
    /**
     * @var string The title of this tanslation
     *
     * @Column(type="text")
     */
    private $content;
    
    /**
     * @param \CalendarBundle\Entity\Nodes\Event $event
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
        $this->_setName($title);
    }
    
    /**
     * @return \CalendarBundle\Entity\Nodes\Event
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
     * @param \CalendarBundle\Entity\Nodes\Translation
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
     * @return \CalendarBundle\Entity\Nodes\Translation
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->_setName($title);
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     *
     * @return \CalendarBundle\Entity\Nodes\Translation
     */
    private function _setName($name)
    {
        $this->name = $this->event->getStartDate()->format('Ymd') . '_' . str_replace(' ', '_', strtolower($name));
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
     * @return \CalendarBundle\Entity\Nodes\Translation
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}