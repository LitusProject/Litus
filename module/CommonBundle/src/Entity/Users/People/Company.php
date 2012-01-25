<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Entity\Users\People;

use CommonBundle\Entity\Users\Credential;

/**
 * This is a person that represents a company.
 *
 * @TODO Build in different company positions
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Users\People\Company")
 * @Table(name="users.people_companies")
 */
class Company extends \CommonBundle\Entity\Users\Person
{
    /**
     * @Column(type="string", length=50)
     */
    private $name;

    /**
     * @Column(name="vat_number", type="string", length=14)
     */
    private $vatNumber;

    /**
     * @param string $username The company's username
     * @param \CommonBundle\Entity\Users\Credential $credential The company's credential
     * @param array $roles The user's roles
     * @param string $firstName The company's first name
     * @param string $lastName The company's last name
     * @param string $email  The user's e-mail address
     * @param string $name The company's name
     * @param string $vatNumber The company's VAT number
     * @param $sex string the sex of the contact person ('m' or 'f')
     * @return \CommonBundle\Entity\Users\People\Company
     *
     */
    public function __construct($username, Credential $credential, array $roles, $firstName, $lastName, $email, $name, $vatNumber, $sex)
    {
        parent::__construct($username, $credential, $roles, $firstName, $lastName, $email, $sex);

        $this->name = $name;
        $this->vatNumber = $vatNumber;
    }

    /**
     * @param $name
     * @return \CommonBundle\Entity\Users\People\Company
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        if (($name === null) || !is_string($name))
            throw new \InvalidArgumentException('Invalid name');
            
        $this->name = $name;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $vatNumber
     * @return \CommonBundle\Entity\Users\People\Company
     * @throws \InvalidArgumentException
     */
    public function setVatNumber($vatNumber)
    {
        if (($vatNumber === null) || !is_string($vatNumber))
            throw new \InvalidArgumentException('Invalid VAT number');
            
        $this->vatNumber = $vatNumber;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }
}
