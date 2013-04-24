<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace MailBundle\Entity\Aliases;

use CommonBundle\Entity\Users\People\Academic as AcademicPerson,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    MailBundle\Entity\MailingList;

/**
 * This is the entity for an academic alias.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Aliases\Academic")
 * @ORM\Table(name="mail.aliases_academic")
 */
class Academic extends \MailBundle\Entity\Alias
{
    /**
     * @var CommonBundle\Entity\Users\People\Academic The academic associated with this alias.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id", nullable=false)
     */
    private $academic;

    /**
     * Creates a new alias for the given academic with the given name.
     *
     * @param string $name The name for this alias.
     * @param \CommonBundle\Entity\Users\People\Academic $academic The academic to create the alias for.
     */
    public function __construct($name, AcademicPerson $academic)
    {
        parent::__construct($name);
        $this->academic = $academic;
    }

    /**
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getAcademic() {
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
