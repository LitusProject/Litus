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

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an external alias.
 *
 * @ORM\Entity(repositoryClass="MailBundle\Repository\Alias\External")
 * @ORM\Table(name="mail.aliases_external")
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
