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
    IntlDateFormatter,
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
     * @return array
     */
    public function getFields()
    {
        $fields = array();
        foreach(parent::getFields() as $field)
            $fields[$field->getStartDate()->getTimestamp() . $field->getId()] = $field;

        ksort($fields);

        return $fields;
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
     * @param \FormBundle\Entity\Mail\Mail|null $reminderMail
     *
     * @return \FormBundle\Entity\Node\Form\Doodle
     */
    public function setReminderMail(Mail $reminderMail = null) {
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
    public function getCompletedReminderMailBody(Entry $entry, Language $language) {
        $body = $this->getMail()->getContent($language);
        $body = str_replace('%id%', $entry->getId(), $body);
        $body = str_replace('%first_name%', $entry->getPersonInfo()->getFirstName(), $body);
        $body = str_replace('%last_name%', $entry->getPersonInfo()->getLastName(), $body);

        $body = str_replace('%entry_summary%', $this->_getSummary($entry, $language), $body);

        return $body;
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

        $formatterDate = new IntlDateFormatter(
            $language->getAbbrev(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'd MMMM Y'
        );

        $formatterHour = new IntlDateFormatter(
            $language->getAbbrev(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'H:mm'
        );

        $result = '';
        foreach ($fieldEntries as $fieldEntry) {
            $result .= $fieldEntry->getField()->getLabel($language);

            if ($fieldEntry->getField()->getLocation($language)) {
                $result .= ': ' . $fieldEntry->getField()->getLocation($language);
            }

            if ($fieldEntry->getField()->getExtraInformation($language)) {
                $result .= PHP_EOL . '    ' . str_replace("\n", "\n    ", str_replace("\r\n", "\n", $fieldEntry->getField()->getExtraInformation($language)));
            }

            $result .= PHP_EOL;
        }

        return $result;
    }
}