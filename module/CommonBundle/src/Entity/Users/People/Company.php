<?php

namespace Litus\Entity\Users\People;

use \Litus\Entity\Users\Credential;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\People\Company")
 * @Table(name="users.people_companies")
 */
class Company extends \Litus\Entity\Users\Person
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
     * @param \Litus\Entity\Users\Credential $credential The company's credential
     * @param array $roles The user's roles
     * @param string $firstName The company's first name
     * @param string $lastName The company's last name
     * @param string $email  The user's e-mail address
     * @param string $name The company's name
     * @param string $vatNumber The company's VAT number
     * @param $sex string the sex of the contact person ('m' or 'f')
     * @return \Litus\Entity\Users\People\Company
     *
     */
    public function __construct($username, Credential $credential, array $roles, $firstName, $lastName, $email, $name, $vatNumber, $sex)
    {
        parent::__construct($username, $credential, $roles, $firstName, $lastName, $email, $sex);

        $this->name = $name;
        $this->vatNumber = $vatNumber;
    }

    /**
     * @throws \InvalidArgumentException
     * @param $name
     * @return Litus\Entity\Users\People\Company
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
     * @throws \InvalidArgumentException
     * @param $vatNumber
     * @return Litus\Entity\Users\People\Company
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
