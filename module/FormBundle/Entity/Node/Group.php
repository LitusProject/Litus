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

namespace FormBundle\Entity\Node;

use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Locale;

/**
 * This entity stores the form group item
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Group")
 * @ORM\Table(name="nodes_forms_groups")
 */
class Group extends \CommonBundle\Entity\Node
{
    /**
     * @var array The translations of this form
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Node\Group\Translation", mappedBy="group", cascade={"remove"})
     */
    private $translations;

    /**
     * @var array The translations of this form
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Node\Form\GroupMap", mappedBy="group", cascade={"remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $forms;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setStartDate(DateTime $startDate)
    {
        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
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
        if (count($this->forms) > 0) {
            return $this->forms[0]->getForm()->getStartDate();
        }
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate(DateTime $endDate)
    {
        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
                $form->getForm()->setEndDate($endDate);
            }
        }

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        if (count($this->forms) > 0) {
            return $this->forms[0]->getForm()->getEndDate();
        }
    }

    /**
     * @param  boolean $active
     * @return self
     */
    public function setActive($active)
    {
        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
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
        if (count($this->forms) > 0) {
            return $this->forms[0]->getForm()->isActive();
        }
    }

    /**
     * @param  integer $max
     * @return self
     */
    public function setMax($max)
    {
        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
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
        if (count($this->forms) > 0) {
            return $this->forms[0]->getForm()->getMax();
        }
    }

    /**
     * @param  boolean $editableByUser
     * @return self
     */
    public function setEditableByUser($editableByUser)
    {
        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
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
        if (count($this->forms) > 0) {
            return $this->forms[0]->getForm()->isEditableByUser();
        }
    }

    /**
     * @param  boolean $nonMember
     * @return self
     */
    public function setNonMember($nonMember)
    {
        if (count($this->forms) > 0) {
            foreach ($this->forms as $form) {
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
        if (count($this->forms) > 0) {
            return $this->forms[0]->getForm()->isNonMember();
        }
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getTitle();
        }

        return '';
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getIntroduction(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getIntroduction();
        }

        return '';
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return \FormBundle\Entity\Node\Group\Translation
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->translations as $translation) {
            if ($language !== null && $translation->getLanguage() == $language) {
                return $translation;
            }

            if ($translation->getLanguage()->getAbbrev() == Locale::getDefault()) {
                $fallbackTranslation = $translation;
            }
        }

        if ($allowFallback && isset($fallbackTranslation)) {
            return $fallbackTranslation;
        }

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
     * @param  Form $form
     * @return integer
     */
    public function getFormNumber(Form $form)
    {
        $i = 1;
        if (count($this->forms) == 0) {
            return 0;
        }

        foreach ($this->forms as $search) {
            if ($search->getForm()->getId() == $form->getId()) {
                return $i;
            }
            $i++;
        }

        return 0;
    }

    /**
     * @param  EntityManager $entityManager
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Indicates whether the given person can edit this form.
     *
     * @param  Person|null $person The person to check.
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if ($person === null) {
            return false;
        }

        if ($this->getCreationPerson()->getId() === $person->getId()) {
            return true;
        }

        foreach ($person->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor') {
                return true;
            }
        }

        return false;
    }

    /**
     * Indicates whether the given person can view this group.
     *
     * @param  Person|null $person The person to check.
     * @return boolean
     */
    public function canBeViewedBy(Person $person = null)
    {
        if (count($this->forms) == 0) {
            return false;
        }

        $this->forms[0]->getForm()->setEntityManager($this->entityManager);

        return $this->forms[0]->getForm()->canBeViewedBy($person);
    }
}
