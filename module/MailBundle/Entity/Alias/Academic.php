<?php

namespace MailBundle\Entity\Alias;

use CommonBundle\Entity\User\Person\Academic as AcademicEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an academic alias.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Alias\Academic")
 * @ORM\Table(name="mail_aliases_academic")
 */
class Academic extends \MailBundle\Entity\Alias
{
    /**
     * @var AcademicEntity The academic associated with this alias.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id", nullable=false)
     */
    private $academic;

    /**
     * Creates a new alias for the given academic with the given name.
     *
     * @param string         $name     The name for this alias.
     * @param AcademicEntity $academic The academic to create the alias for.
     */
    public function __construct($name, AcademicEntity $academic)
    {
        parent::__construct($name);
        $this->academic = $academic;
    }

    /**
     * @return AcademicEntity
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->academic->getEmail();
    }
}
