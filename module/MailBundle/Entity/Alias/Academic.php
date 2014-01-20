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

namespace MailBundle\Entity\Alias;

use CommonBundle\Entity\User\Person\Academic as AcademicEntity,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    MailBundle\Entity\MailingList;

/**
 * This is the entity for an academic alias.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Alias\Academic")
 * @ORM\Table(name="mail.aliases_academic")
 */
class Academic extends \MailBundle\Entity\Alias
{
    /**
     * @var CommonBundle\Entity\User\Person\Academic The academic associated with this alias.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id", nullable=false)
     */
    private $academic;

    /**
     * Creates a new alias for the given academic with the given name.
     *
     * @param string $name The name for this alias.
     * @param \CommonBundle\Entity\User\Person\Academic $academic The academic to create the alias for.
     */
    public function __construct($name, AcademicEntity $academic)
    {
        parent::__construct($name);
        $this->academic = $academic;
    }

    /**
     * @return \CommonBundle\Entity\User\Person\Academic
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
