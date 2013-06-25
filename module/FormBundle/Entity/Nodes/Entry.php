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

namespace FormBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Nodes\Entry")
 * @ORM\Table(name="nodes.form_entries")
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
     * @var \DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var \CommonBundle\Entity\User\Person The person who created this node
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id", nullable=true)
     */
    private $creationPerson;

    /**
     * @var \FormBundle\Entity\Nodes\GuestInfo The guest who created this node
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Nodes\GuestInfo")
     * @ORM\JoinColumn(name="guest_info", referencedColumnName="id", nullable=true)
     */
    private $guestInfo;

    /**
     * @var FormBundle\Entity\Nodes\Form The form this entry is part of.
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Nodes\Form")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    private $form;

    /**
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Entry", mappedBy="formEntry", cascade={"all"})
     */
    private $fieldEntries;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \FormBundle\Entity\Nodes\Form $form
     */
    public function __construct($person, $guestInfo, $form)
    {
        $this->creationTime = new DateTime();
        $this->creationPerson = $person;
        $this->guestInfo = $guestInfo;
        $this->form = $form;
        $this->fieldEntries = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getCreationPerson()
    {
        return $this->creationPerson;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getGuestInfo()
    {
        return $this->guestInfo;
    }

    /**
     * @return Information about the person.
     */
    public function getPersonInfo()
    {
        if (!$this->isGuestEntry())
            return $this->creationPerson;
        else
            return $this->guestInfo;
    }

    public function isGuestEntry()
    {
        return $this->creationPerson === null;
    }

    /**
     * @return The form this field belongs to.
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @param FormBundle\Entity\Entry The entry to add to this form.
     */
    public function addFieldEntry($fieldEntry) {
        $this->fieldEntries->add($fieldEntry);
        return $this;
    }

    public function getFieldEntries() {
        return $this->fieldEntries->toArray();
    }
}
