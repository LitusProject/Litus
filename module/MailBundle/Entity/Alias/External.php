<?php

namespace MailBundle\Entity\Alias;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an external alias.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Alias\External")
 * @ORM\Table(name="mail_aliases_external")
 */
class External extends \MailBundle\Entity\Alias
{
    /**
     * @var string The e-mail address of this alias
     *
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * Creates a new alias with the given name and emailaddress.
     *
     * @param string $name  The name for this alias
     * @param string $email The e-mail address for this alias
     */
    public function __construct($name, $email)
    {
        parent::__construct($name);

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->email;
    }
}
