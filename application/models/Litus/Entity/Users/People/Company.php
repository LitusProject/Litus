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
     * @param string $firstName The company's first name
     * @param string $lastName The company's last name
     * @param string $email  The user's e-mail address
     * @param string $name The company's name
     * @param string $vatNumber The company's VAT number
     */
    public function __construct($username, Credential $credential, $firstName, $lastName, $email, $name, $vatNumber)
    {
        parent::__construct($username, $credential, $firstName, $lastName, $email);

        $this->name = $name;
        $this->vatNumber = $vatNumber;
    }

    /**
     * @param $name
     * @return Litus\Entity\Users\People\Company
     */
    public function setName($name)
    {
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
     * @return Litus\Entity\Users\People\Company
     */
    public function setVatNumber($vatNumber)
    {
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