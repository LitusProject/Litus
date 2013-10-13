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

namespace FormBundle\Entity\Node\Form;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Mail\Mail,
    FormBundle\Entity\Node\Entry,
    FormBundle\Entity\Node\Form as BaseForm;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Form\Doodle")
 * @ORM\Table(name="nodes.forms_doodles")
 */
class Doodle extends BaseForm
{
    /**
     * @var boolean Flag whether the names of reservations are visible for others
     *
     * @ORM\Column(name="names_visible_for_others", type="boolean")
     */
    private $namesVisibleForOthers;

    /**
     * @var \FormBundle\Entity\Mail\Mail The mail sent for reminding.
     *
     * @ORM\OneToOne(targetEntity="FormBundle\Entity\Mail\Mail")
     * @ORM\JoinColumn(name="reminder_mail", referencedColumnName="id")
     */
    private $reminderMail;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param boolean $active
     * @param boolean $multiple
     * @param boolean $nonMember
     * @param boolean $editableByUser
     * @param boolean $namesVisibleForOthers
     */
    public function __construct(Person $person, DateTime $startDate, DateTime $endDate, $active, $multiple, $nonMember, $editableByUser, $namesVisibleForOthers)
    {
        parent::__construct($person, $startDate, $endDate, $active, 0, $multiple, $nonMember, $editableByUser);

        $this->namesVisibleForOthers = $namesVisibleForOthers;
    }

    /**
     * @param boolean $namesVisibleForOthers
     * @return \FormBundle\Entity\Node\Form\Doodle
     */
    public function setNamesVisibleForOthers($namesVisibleForOthers)
    {
        $this->namesVisibleForOthers = $namesVisibleForOthers;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getNamesVisibleForOthers()
    {
        return $this->namesVisibleForOthers;
    }

    /**
     * @return boolean
     */
    public function hasReminderMail() {
        return null !== $this->reminderMail;
    }

    /**
     * @param \FormBundle\Entity\Mail\Mail $reminderMail
     *
     * @return \FormBundle\Entity\Node\Form\Doodle
     */
    public function setReminderMail(Mail $reminderMail) {
        $this->reminderMail = $reminderMail;
        return $this;
    }

    /**
     * @return \FormBundle\Entity\Mail\Mail
     */
    public function getReminderMail() {
        return $this->reminderMail;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'doodle';
    }

    /**
     * @param \FormBundle\Entity\Node\Entry $entry
     * @param \CommonBundle\Entity\General\Language $language
     * @return string
     */
    protected function _getSummary(Entry $entry, Language $language) {
        $fieldEntries = $this->_entityManager
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByFormEntry($entry);

        $result = '';
        foreach ($fieldEntries as $fieldEntry) {
            $result = $result . $fieldEntry->getField()->getLabel($language) . ': ' . $fieldEntry->getValueString($language) . '
';
        }

        return $result;
    }
}