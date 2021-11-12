<?php

namespace MailBundle\Entity\MailingList\Entry\Person;

use CommonBundle\Entity\User\Person\Academic as AcademicEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for the list entry of an academic.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\Entry\Person\Academic")
 * @ORM\Table(name="mail_lists_entries_people_academic")
 */
class Academic extends \MailBundle\Entity\MailingList\Entry\Person
{
    /**
     * @var AcademicEntity The academic associated with this entry.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id", nullable=false)
     */
    private $academic;

    /**
     * @return AcademicEntity
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @param  \CommonBundle\Entity\User\Person\Academic $academic
     * @return self
     */
    public function setAcademic(AcademicEntity $academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->academic->getEmail();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->academic->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->academic->getLastName();
    }
}
