<?php

namespace MailBundle\Entity\MailingList;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a named list.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\MailingList\Named")
 * @ORM\Table(name="mail_lists_named")
 */
class Named extends \MailBundle\Entity\MailingList
{
    /**
     * @var string The name of this list
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * Creates a new list with the given name
     *
     * @param string $name The name for this list
     */
    public function __construct($name)
    {
        parent::__construct();
        $this->setName($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name The name
     * @return self
     */
    public function setName($name)
    {
        $this->name = strtolower($name);

        return $this;
    }
}
