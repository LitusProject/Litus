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

namespace FormBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the form group item
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Group")
 * @ORM\Table(name="nodes.form_groups")
 */
class Group extends \CommonBundle\Entity\Node
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
     * @var array The translations of this form
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Node\Translation\Group", mappedBy="group", cascade={"remove"})
     */
    private $translations;

    /**
     * @var array The translations of this form
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Node\Group\Mapping", mappedBy="group", cascade={"remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $forms;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->forms[0]->getForm()->getStartDate();
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->forms[0]->getForm()->getEndDate();
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->forms[0]->getForm()->isActive();
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getTitle();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getIntroduction(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getIntroduction();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return \FormBundle\Entity\Node\Translation\Group
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback && isset($fallbackTranslation))
            return $fallbackTranslation;

        return null;
    }

    /**
     * @return array
     */
    public function getForms()
    {
        return $this->forms;
    }
}