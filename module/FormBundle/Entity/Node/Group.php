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

namespace FormBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
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
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_entityManager;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);
    }

    /**
     * @param DateTime $startDate
     *
     * @return \FormBundle\Entity\Node\Group
     */
    public function setStartDate($startDate)
    {
        if (sizeof($this->forms) > 0) {
            foreach($this->forms as $form) {
                $form->getForm()->setStartDate($startDate);
            }
        }

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        if (sizeof($this->forms) > 0)
            return $this->forms[0]->getForm()->getStartDate();
    }

    /**
     * @param DateTime $endDate
     *
     * @return \FormBundle\Entity\Node\Group
     */
    public function setEndDate($endDate)
    {
        if (sizeof($this->forms) > 0) {
            foreach($this->forms as $form) {
                $form->getForm()->setEndDate($endDate);
            }
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        if (sizeof($this->forms) > 0)
            return $this->forms[0]->getForm()->getEndDate();
    }

    /**
     * @param boolean $active
     *
     * @return \FormBundle\Entity\Node\Group
     */
    public function setActive($active)
    {
        if (sizeof($this->forms) > 0) {
            foreach($this->forms as $form) {
                $form->getForm()->setActive($active);
            }
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        if (sizeof($this->forms) > 0)
            return $this->forms[0]->getForm()->isActive();
    }

    /**
     * @param int $max
     *
     * @return \FormBundle\Entity\Node\Group
     */
    public function setMax($max)
    {
        if (sizeof($this->forms) > 0) {
            foreach($this->forms as $form) {
                $form->getForm()->setMax($max);
            }
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getMax()
    {
        if (sizeof($this->forms) > 0)
            return $this->forms[0]->getForm()->getMax();
    }

    /**
     * @param boolean $editableByUser
     *
     * @return \FormBundle\Entity\Node\Group
     */
    public function setEditableByUser($editableByUser)
    {
        if (sizeof($this->forms) > 0) {
            foreach($this->forms as $form) {
                $form->getForm()->setEditableByUser($editableByUser);
            }
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEditableByUser()
    {
        if (sizeof($this->forms) > 0)
            return $this->forms[0]->getForm()->isEditableByUser();
    }

    /**
     * @param boolean $nonMember
     *
     * @return \FormBundle\Entity\Node\Group
     */
    public function setNonMember($nonMember)
    {
        if (sizeof($this->forms) > 0) {
            foreach($this->forms as $form) {
                $form->getForm()->setNonMember($nonMember);
            }
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNonMember()
    {
        if (sizeof($this->forms) > 0)
            return $this->forms[0]->getForm()->isNonMember();
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

    /**
     * @param \FormBundle\Entity\Node\Form $form
     * @return integer
     */
    public function getFormNumber(Form $form)
    {
        $i = 1;
        if (sizeof($this->forms) == 0)
            return 0;

        foreach($this->forms as $search) {
            if ($search->getForm()->getId() == $form->getId())
                return $i;
            $i++;
        }
        return 0;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return \FormBundle\Entity\Node\Form
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }

    /**
     * Indicates whether the given person can edit this form.
     *
     * @param \CommonBundle\Entity\User\Person $person The person to check.
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if (null === $person)
            return false;

        if ($this->getCreationPerson()->getId() === $person->getId())
            return true;

        foreach ($person->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor')
                return true;
        }

        return false;
    }

    /**
     * Indicates whether the given person can view this group.
     *
     * @param \CommonBundle\Entity\User\Persons $person The person to check.
     * @return boolean
     */
    public function canBeViewedBy(Person $person = null)
    {
        if (sizeof($this->forms) == 0)
            return false;

        $this->forms[0]->getForm()->setEntityManager($this->_entityManager);
        return $this->forms[0]->getForm()->canBeViewedBy($person);
    }
}