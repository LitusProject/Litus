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

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Entry as FieldEntry;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Entry")
 * @ORM\Table(name="nodes.forms_entries")
 */
class Entry
{
    /**
     * @var int The ID of this node
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
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\GuestInfo")
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
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Entry", mappedBy="formEntry", cascade={"all"})
     */
    private $fieldEntries;

    /**
     * @param Person|null    $person
     * @param GuestInfo|null $guestInfo
     * @param Form           $form
     * @param boolean        $draft
     */
    public function __construct(Person $person = null, GuestInfo $guestInfo = null, Form $form, $draft = false)
    {
        $this->creationTime = new DateTime();
        $this->creationPerson = $person;
        $this->guestInfo = $guestInfo;
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
     * @return Person|GuestInfo
     */
    public function getPersonInfo()
    {
        if (!$this->isGuestEntry())
            return $this->creationPerson;
        else
            return $this->guestInfo;
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
     * @param FieldEntry $fieldEntry The entry to add to this form.
     * @return self
     */
    public function addFieldEntry(FieldEntry $fieldEntry)
    {
        $this->fieldEntries->add($fieldEntry);

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
