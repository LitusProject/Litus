<?php

namespace Litus\Entities\Br\Contracts;

use Zend\Pdf\Pdf;
use Zend\Pdf\Page;

use Doctrine\Common\Collections\ArrayCollection;

use Litus\Entities\Users\Person;
use Litus\Entities\Users\People\Company;

/**
 *
 * @Entity(repositoryClass="Litus\Repositories\Br\Contracts\ContractRepository")
 * @Table(name="br.contract")
 */
class Contract {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     *
     * @var int
     */
    private $id;

    /**
     * @OneToMany(targetEntity="Litus\Entities\Br\Contracts\ContractComposition", mappedBy="contract")
     *
     * @var array
     */
    private $sections;

    /**
     * @Column(type="datetime")
     *
     * @var \DateTime
     */
    private $date;

    /**
     * @ManyToOne(targetEntity="Litus\Entities\Users\Person", fetch="LAZY")
     * @JoinColumn(name="author", referencedColumnName="id", nullable="false")
     *
     * @var Person
     */
    private $author;

    /**
     * @ManyToOne(targetEntity="Litus\Entities\Users\People\Company", fetch="LAZY")
     * @JoinColumn(name="company", referencedColumnName="id", nullable="false")
     *
     * @var Company
     */
    private $company;

    public function __construct()
    {
        $this->parts = new ArrayCollection();
    }

    /**
     * Returns all the Parts of this contract.
     *
     * @return array
     */
    public function getParts()
    {
        return $this->parts->toArray();
    }

    /**
     * Adds the given part at the given order to this Contract.
     *
     * @param Section $section
     * @param int $order
     * @return void
     */
    public function addSection(Section $section, $order)
    {
        $this->parts->add(new ContractComposition($this, $section, $order));
    }

    /**
     * @return Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @throws \Exception
     * @param \Litus\Entities\Users\Person $author
     * @return void
     */
    public function setAuthor(Person $author)
    {
        if($author === null)
            throw new \Exception('Author cannot be null.');
        $this->author = $author;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany(Company $company)
    {
        if($company === null)
            throw new \Exception('Company cannot be null.');
        $this->company = $company;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\DateTime $date = null)
    {
        if($date === null)
            $this->date = new \DateTime();
        else
            $this->date = $date;
    }
}
