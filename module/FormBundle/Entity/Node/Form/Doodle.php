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
     * @param \CommonBundle\Entity\User\Person $person
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param boolean $active
     * @param boolean $multiple
     * @param boolean $nonMember
     * @param boolean $editableByUser
     * @param boolean $mail Whether to send a mail upon completion.
     * @param string $mailSubject The subject of the mail.
     * @param string $mailBody The body of the mail.
     * @param string $mailFrom
     * @param string $mailBcc
     */
    public function __construct(Person $person, DateTime $startDate, DateTime $endDate, $active, $multiple, $nonMember, $editableByUser, $mail, $mailSubject, $mailBody, $mailFrom, $mailBcc)
    {
        parent::__construct($person, $startDate, $endDate, $active, 0, $multiple, $nonMember, $editableByUser, $mail, $mailSubject, $mailBody, $mailFrom, $mailBcc);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'doodle';
    }
}