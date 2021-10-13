<?php

namespace MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an alias.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Alias")
 * @ORM\Table(name="mail_aliases")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="MailBundle\Entity\Alias\Academic",
 *      "external"="MailBundle\Entity\Alias\External"
 * })
 */
abstract class Alias
{
    /**
     * @var integer The entry's unique identifier
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
     * @return integer
     */
    public function getId()
    {
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
