<?php

namespace FormBundle\Entity\Node\Group;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Node\Group;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Group\Translation")
 * @ORM\Table(name="nodes_forms_groups_translations")
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
     * @var Group The group of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Group", inversedBy="translations")
     * @ORM\JoinColumn(name="form_group", referencedColumnName="id")
     */
    private $group;

    /**
     * @var Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The title of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string The introduction of this tanslation
     *
     * @ORM\Column(type="text")
     */
    private $introduction;

    /**
     * @param Group    $group
     * @param Language $language
     * @param string   $title
     * @param string   $introduction
     */
    public function __construct(Group $group, Language $language, $title, $introduction)
    {
        $this->group = $group;
        $this->language = $language;
        $this->title = $title;
        $this->introduction = $introduction;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
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
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * @param  string $introduction
     * @return self
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }
}
