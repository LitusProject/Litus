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
    FormBundle\Entity\Node\Entry,
    FormBundle\Entity\Mail\Mail,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Field;

/**
 * This entity stores the form
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Form")
 * @ORM\Table(name="nodes.forms")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "form"="FormBundle\Entity\Node\Form\Form",
 *      "doodle"="FormBundle\Entity\Node\Form\Doodle"
 * })
 */
abstract class Form extends \CommonBundle\Entity\Node
{
    /**
     * @var int The ID of this form
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var int The maximum number of entries of this form.
     *
     * @ORM\Column(name="max", type="integer")
     */
    private $max;

    /**
     * @var boolean Indicates whether submitters can submit more than different one answer.
     *
     * @ORM\Column(name="multiple", type="boolean")
     */
    private $multiple;

    /**
     * @var boolean Indicates whether submitters can be non members.
     *
     * @ORM\Column(name="non_member", type="boolean")
     */
    private $nonMember;

    /**
     * @var boolean Form editable by user
     *
     * @ORM\Column(name="editable_by_user", type="boolean")
     */
    private $editableByUser;

    /**
     * @var boolean Send a mail to guests after submitting form to login later and edit/view their submission
     *
     * @ORM\Column(name="send_guest_login_mail", type="boolean")
     */
    private $sendGuestLoginMail;

    /**
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Field", mappedBy="form")
     *
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $fields;

    /**
     * @var DateTime The start date and time of this form.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this form.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var boolean The flag whether the banner is active or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var \FormBundle\Entity\Mail\Mail The mail sent upon completion.
     *
     * @ORM\OneToOne(targetEntity="FormBundle\Entity\Mail\Mail")
     * @ORM\JoinColumn(name="mail", referencedColumnName="id")
     */
    private $mail;

    /**
     * @var array The translations of this form
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Node\Translation\Form", mappedBy="form", cascade={"remove"})
     */
    private $translations;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_entityManager;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \DateTime                        $startDate
     * @param \DateTime                        $endDate
     * @param boolean                          $active
     * @param boolean                          $max
     * @param boolean                          $multiple
     * @param boolean                          $nonMember
     * @param boolean                          $editableByUser
     * @param boolean                          $sendGuestLoginMail
     */
    public function __construct(Person $person, DateTime $startDate, DateTime $endDate, $active, $max, $multiple, $nonMember, $editableByUser, $sendGuestLoginMail)
    {
        parent::__construct($person);

        $this->max = $max;
        $this->multiple = $multiple;
        $this->nonMember = $nonMember;
        $this->editableByUser = $editableByUser;
        $this->sendGuestLoginMail = $sendGuestLoginMail;
        $this->fields = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->active = $active;
    }

    /**
     * @param int $max
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param boolean $multiple
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param boolean $editableByUser
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setEditableByUser($editableByUser)
    {
        $this->editableByUser = $editableByUser;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEditableByUser()
    {
        return $this->editableByUser;
    }

    /**
     * @param boolean $nonMember
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setNonMember($nonMember)
    {
        $this->nonMember = $nonMember;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNonMember()
    {
        return $this->nonMember;
    }

    /**
     * @param boolean $sendGuestLoginMail
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setSendGuestLoginMail($sendGuestLoginMail)
    {
        $this->sendGuestLoginMail = $sendGuestLoginMail;

        return $this;
    }

    /**
     * @return boolean
     */
    public function sendGuestLoginMail()
    {
        return $this->sendGuestLoginMail;
    }

    /**
     * @param \FormBundle\Entity\Field The field to add to this form.
     */
    public function addField($field)
    {
        $this->fields->add($field);

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields->toArray();
    }

    /**
     * @param DateTime $startDate
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param boolean $active
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return boolean
     */
    public function hasMail()
    {
        return null !== $this->mail;
    }

    /**
     * @param \FormBundle\Entity\Mail\Mail|null $mail
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setMail(Mail $mail = null)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return \FormBundle\Entity\Mail\Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
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
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
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
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
     * @return string
     */
    public function getSubmitText(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getSubmitText();

        return '';
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
     * @return string
     */
    public function getUpdateText(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getUpdateText();

        return '';
    }

    /**
     * @param  \CommonBundle\Entity\General\Language    $language
     * @param  boolean                                  $allowFallback
     * @return \FormBundle\Entity\Node\Translation\Form
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->translations as $translation) {
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
     * Indicates whether the given person can view this form.
     *
     * @param  \CommonBundle\Entity\User\Persons $person The person to check.
     * @return boolean
     */
    public function canBeViewedBy(Person $person = null)
    {
        if (null === $person)
            return false;

        $result = $this->_entityManager
            ->getRepository('FormBundle\Entity\ViewerMap')
            ->findOneByPersonAndForm($person, $this);

        return $result !== null;
    }

    /**
     * Indicates whether the given person can edit this form.
     *
     * @param  \CommonBundle\Entity\User\Person $person The person to check.
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
     * Returns the value for the given entry and field.
     *
     * @param \FormBundle\Entity\Node\Entry         $entry    The entry to find the value for.
     * @param \FormBundle\Entity\Field              $field    The field to find the value for.
     * @param \CommonBundle\Entity\General\Language $language The language to get the value in.
     *
     * @return The value.
     */
    public function getValueFor(Entry $entry, Field $field, Language $language)
    {
        foreach ($entry->getFieldEntries() as $fieldEntry) {
            if ($fieldEntry->getField()->getId() === $field->getId()) {
                return $fieldEntry->getValueString($language);
            }
        }

        return '';
    }

    /**
     * @param  \Doctrine\ORM\EntityManager  $entityManager
     * @return \FormBundle\Entity\Node\Form
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;

        return $this;
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @param  \FormBundle\Entity\Node\Entry         $entry
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  string                                $url
     * @return string
     */
    public function getCompletedMailBody(Entry $entry, Language $language, $url)
    {
        $body = $this->getMail()->getContent($language);
        $body = str_replace('%id%', $entry->getId(), $body);
        $body = str_replace('%first_name%', $entry->getPersonInfo()->getFirstName(), $body);
        $body = str_replace('%last_name%', $entry->getPersonInfo()->getLastName(), $body);

        $body = str_replace('%entry_summary%', $this->_getSummary($entry, $language), $body);

        if ($this->sendGuestLoginMail() && $entry->isGuestEntry()) {
            $body = str_replace('#guest_login_text#', '', $body);
            $body = str_replace('%guest_login%', $url, $body);
        } else {
            $body = preg_replace('/#guest_login_text#.*#guest_login_text#\%guest_login\%/', '', $body);
        }

        return $body;
    }

    /**
     * @param  \FormBundle\Entity\Node\Entry         $entry
     * @param  \CommonBundle\Entity\General\Language $language
     * @return string
     */
    abstract protected function _getSummary(Entry $entry, Language $language);
}
