<?php

namespace Litus\Entity\Users\People;

/**
 * @Entity(repositoryClass="Litus\Repository\Users\People\Company")
 * @Table(name="users.people_companies")
 */
class Company extends \Litus\Entity\Users\Person
{
    /**
     * @Column(type="string")
     */
    private $name;

    /**
     * @Column(name="vat_number", type="string")
     */
    private $vatNumber;
}