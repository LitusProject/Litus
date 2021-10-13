<?php

namespace FormBundle\Entity\Node;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Entry as FieldEntry;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Entry")
 * @ORM\Table(name="nodes_forms_entries")
 */
class Entry
{
    /**
     * @var integer The ID of this node
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var Person|null The person who created this node
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id", nullable=true)
     */
    private $creationPerson;

    /**
     * @var GuestInfo|null The guest who created this node
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\GuestInfo", cascade={"all"})
     * @ORM\JoinColumn(name="guest_info", referencedColumnName="id", nullable=true)
     */
    private $guestInfo;

    /**
     * @var Form The form this entry is part of.
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Form")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    private $form;

    /**
     * @var boolean Flag whether this entry is a draft version.
     *
     * @ORM\Column(type="boolean")
     */
    private $draft;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Entry", mappedBy="formEntry", cascade={"all"}, orphanRemoval=true)
     */
    private $fieldEntries;

    /**
     * @param Form        $form
     * @param Person|null $person
     * @param boolean     $draft
     */
    public function __construct(Form $form, Person $person = null, $draft = false)
    {
        $this->creationTime = new DateTime();
        $this->creationPerson = $person;
        $this->form = $form;
        $this->fieldEntries = new ArrayCollection();
        $this->draft = $draft;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return Person|null
     */
    public function getCreationPerson()
    {
        return $this->creationPerson;
    }

    /**
     * @return GuestInfo|null
     */
    public function getGuestInfo()
    {
        return $this->guestInfo;
    }

    /**
     * @param  GuestInfo|null
     * @return self
     */
    public function setGuestInfo(GuestInfo $guestInfo = null)
    {
        $this->guestInfo = $guestInfo;

        return $this;
    }

    /**
     * @return Person|GuestInfo
     */
    public function getPersonInfo()
    {
        if (!$this->isGuestEntry()) {
            return $this->creationPerson;
        } else {
            return $this->guestInfo;
        }
    }

    /**
     * @return boolean
     */
    public function isGuestEntry()
    {
        return $this->creationPerson === null;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param  FieldEntry $fieldEntry The entry to add to this form entry.
     * @return self
     */
    public function addFieldEntry(FieldEntry $fieldEntry)
    {
        $this->fieldEntries->add($fieldEntry);

        return $this;
    }

    /**
     * @param  FieldEntry $fieldEntry The entry to remove from this form entry.
     * @return self
     */
    public function removeFieldEntry(FieldEntry $fieldEntry)
    {
        $this->fieldEntries->removeElement($fieldEntry);

        return $this;
    }

    /**
     * @return array
     */
    public function getFieldEntries()
    {
        return $this->fieldEntries->toArray();
    }

    /**
     * @param  boolean $draft
     * @return self
     */
    public function setDraft($draft)
    {
        $this->draft = $draft;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDraft()
    {
        return $this->draft;
    }
}
