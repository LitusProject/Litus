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

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
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
     * @var boolean The flag whether a reminder mail will be sent upon completion.
     *
     * @ORM\Column(name="reminder_mail", type="boolean")
     */
    private $reminderMail;

    /**
     * @var string The subject of the mail sent upon completion.
     *
     * @ORM\Column(name="reminder_mail_subject", type="text")
     */
    private $reminderMailSubject;

    /**
     * @var string The body of the mail sent upon completion.
     *
     * @ORM\Column(name="reminder_mail_body", type="text")
     */
    private $reminderMailBody;

    /**
     * @var string The email address from which the mail is sent.
     *
     * @ORM\Column(name="reminder_mail_from", type="text")
     */
    private $reminderMailFrom;

    /**
     * @var boolean Whether to send a copy to the sender or not.
     *
     * @ORM\Column(name="reminder_mail_bcc", type="boolean")
     */
    private $reminderMailBcc;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param boolean $active
     * @param boolean $multiple
     * @param boolean $nonMember
     * @param boolean $editableByUser
     * @param boolean $namesVisibleForOthers
     * @param boolean $mail Whether to send a mail upon completion.
     * @param string $mailSubject The subject of the mail.
     * @param string $mailBody The body of the mail.
     * @param string $mailFrom
     * @param string $mailBcc
     * @param boolean $reminderMail Whether to send a reminder mail upon completion.
     * @param string $reminderMailSubject The subject of the mail.
     * @param string $reminderMailBody The body of the mail.
     * @param string $reminderMailFrom
     * @param string $reminderMailBcc
     */
    public function __construct(Person $person, DateTime $startDate, DateTime $endDate, $active, $multiple, $nonMember, $editableByUser, $namesVisibleForOthers, $mail, $mailSubject, $mailBody, $mailFrom, $mailBcc, $reminderMail, $reminderMailSubject, $reminderMailBody, $reminderMailFrom, $reminderMailBcc)
    {
        parent::__construct($person, $startDate, $endDate, $active, 0, $multiple, $nonMember, $editableByUser, $mail, $mailSubject, $mailBody, $mailFrom, $mailBcc);

        $this->namesVisibleForOthers = $namesVisibleForOthers;
        $this->reminderMail = $reminderMail;
        $this->reminderMailSubject = $reminderMailSubject;
        $this->reminderMailBody = $reminderMailBody;
        $this->reminderMailFrom = $reminderMailFrom;
        $this->reminderMailBcc = $reminderMailBcc;
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
     * @param boolean $reminderMail
     *
     * @return \FormBundle\Entity\Node\Form\Doodle
     */
    public function setReminderMail($reminderMail) {
        $this->reminderMail = $reminderMail;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasReminderMail() {
        return $this->reminderMail;
    }

    /**
     * @param boolean $reminderMailSubject
     *
     * @return \FormBundle\Entity\Node\Form\Doodle
     */
    public function setReminderMailSubject($reminderMailSubject) {
        $this->reminderMailSubject = $reminderMailSubject;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getReminderMailSubject() {
        return $this->reminderMailSubject;
    }

    /**
     * @param boolean $reminderMailBody
     *
     * @return \FormBundle\Entity\Node\Form\Doodle
     */
    public function setReminderMailBody($reminderMailBody) {
        $this->reminderMailBody = $reminderMailBody;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getReminderMailBody() {
        return $this->reminderMailBody;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \FormBundle\Entity\Node\Entry $entry
     * @param \CommonBundle\Entity\General\Language $language
     * @return string
     */
    public function getCompletedReminderMailBody(EntityManager $entityManager, Entry $entry, Language $language) {
        $body = $this->getReminderMailBody();
        $body = str_replace('%id%', $entry->getId(), $body);
        $body = str_replace('%first_name%', $entry->getPersonInfo()->getFirstName(), $body);
        $body = str_replace('%last_name%', $entry->getPersonInfo()->getLastName(), $body);

        $body = str_replace('%entry_summary%', $this->_getReminderSummary($entityManager, $entry, $language), $body);

        return $body;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \FormBundle\Entity\Node\Entry $entry
     * @param \CommonBundle\Entity\General\Language $language
     * @return string
     */
    private function _getReminderSummary(EntityManager $entityManager, Entry $entry, Language $language) {
        $fieldEntries = $entityManager->getRepository('FormBundle\Entity\Entry')
            ->findAllByFormEntry($entry);

        $result = '';
        foreach ($fieldEntries as $fieldEntry) {
            $result = $result . $fieldEntry->getField()->getLabel($language) . ': ' . $fieldEntry->getValueString($language) . '
';
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getReminderMailFrom() {
        return $this->reminderMailFrom;
    }

    /**
     * @param string $reminderMailFrom
     *
     * @return \FromBundle\Entity\Nodes\Form\Doodle
     */
    public function setReminderMailFrom($reminderMailFrom) {
        $this->reminderMailFrom = $reminderMailFrom;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getReminderMailBcc() {
        return $this->reminderMailBcc;
    }

    /**
     * @param boolean $reminderMailBcc
     *
     * @return \FromBundle\Entity\Nodes\Form\Doodle
     */
    public function setReminderMailBcc($reminderMailBcc) {
        $this->reminderMailBcc = $reminderMailBcc;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'doodle';
    }
}