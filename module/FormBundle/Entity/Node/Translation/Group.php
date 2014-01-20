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

namespace FormBundle\Entity\Node\Translation;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Node\Group as GroupEntity;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Translation\Group")
 * @ORM\Table(name="nodes.form_groups_translations")
 */
class Group
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
     * @var \FormBundle\Entity\Node\Group The group of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Group", inversedBy="translations")
     * @ORM\JoinColumn(name="form_group", referencedColumnName="id")
     */
    private $group;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
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
     * @param \FormBundle\Entity\Node\Group $group
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $title
     * @param string $introduction
     */
    public function __construct(GroupEntity $group, Language $language, $title, $introduction)
    {
        $this->group = $group;
        $this->language = $language;
        $this->title = $title;
        $this->introduction = $introduction;
    }

    /**
     * @return \FormBundle\Entity\Node\Group
     */
    public function getGroup()
    {
        return $this->group;
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
     * @return \FormBundle\Entity\Node\Translation\Group
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
     * @param string $introduction
     *
     * @return \FormBundle\Entity\Node\Translation\Group
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;
        return $this;
    }
}
