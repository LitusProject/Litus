<?php

namespace Litus\Entity\Br\Contracts;

use \Zend\Pdf\Pdf;
use \Zend\Pdf\Page;

use \Doctrine\Common\Collections\ArrayCollection;

use \Litus\Entity\Users\Person;
use \Litus\Entity\Users\People\Company;

use \InvalidArgumentException;
use \DateTime;

/**
 *
 * @Entity(repositoryClass="Litus\Repository\Br\Contracts\ContractRepository")
 * @Table(name="br.contract")
 */
class Contract
{
    /**
     * @var int A generated ID
     *
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue
     */
    private $id;

    /**
     * @var array The sections this contract contains
     *
     * @OneToMany(targetEntity="Litus\Entity\Br\Contracts\ContractComposition", mappedBy="contract")
     */
    private $sections;

    /**
     * @var DateTime The date and time when this contract was written
     *
     * @Column(type="datetime")
     */
    private $date;

    /**
     * @var Person The author of this contract
     *
     * @ManyToOne(targetEntity="Litus\Entity\Users\Person", fetch="LAZY")
     * @JoinColumn(name="author", referencedColumnName="id", nullable="false")
     */
    private $author;

    /**
     * @var Company The company for which this contract is meant
     *
     * @ManyToOne(targetEntity="Litus\Entity\Users\People\Company", fetch="LAZY")
     * @JoinColumn(name="company", referencedColumnName="id", nullable="false")
     */
    private $company;

    public function __construct()
    {
        $this->parts = new ArrayCollection();
        setDate();
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
     * Adds the given part at the given order to this contract
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
     * @throws InvalidArgumentException
     * @param \Litus\Entity\Users\Person $author
     * @return void
     */
    public function setAuthor(Person $author)
    {
        if ($author === null)
            throw new InvalidArgumentException('Author cannot be null.');
        $this->author = $author;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @throws InvalidArgumentException
     * @param \Litus\Entity\Users\People\Company $company
     * @return void
     */
    public function setCompany(Company $company)
    {
        if ($company === null)
            throw new InvalidArgumentException('Company cannot be null.');
        $this->company = $company;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date if $date is null, the current date and time are used.
     * @return void
     */
    public function setDate(\DateTime $date = null)
    {
        if ($date === null)
            $this->date = new \DateTime();
        else
            $this->date = $date;
    }
}
