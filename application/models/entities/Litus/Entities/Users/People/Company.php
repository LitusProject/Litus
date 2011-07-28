<?php

namespace Litus\Entities\Users\People;

/**
 * @Entity(repositoryClass="Litus\Repositories\Users\People\CompanyRepository")
 * @Table(name="users.people_companies")
 */
class Company extends \Litus\Entities\Users\Person
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