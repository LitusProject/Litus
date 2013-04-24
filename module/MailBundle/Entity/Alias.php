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

namespace MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for an alias.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Alias")
 * @ORM\Table(name="mail.aliases")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="MailBundle\Entity\Aliases\Academic",
 *      "external"="MailBundle\Entity\Aliases\External"
 * })
 */
abstract class Alias
{
    /**
     * @var The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of this alias
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * Creates a new alias with the given name.
     *
     * @param string $name The name for this alias.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    abstract public function getEmailAddress();
}
