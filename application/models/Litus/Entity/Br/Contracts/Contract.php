<?php

namespace Litus\Entity\Br\Contracts;

use \Doctrine\Common\Collections\ArrayCollection;

use \Litus\Entity\Users\People\Company;
use \Litus\Entity\Users\Person;

use \Zend\Pdf\Page;
use \Zend\Pdf\Pdf;

/**
 * @Entity(repositoryClass="Litus\Repository\Br\Contracts\Contract")
 * @Table(name="br.contract")
 */
class Contract {

    /**
     * @var int
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany(targetEntity="Litus\Entity\Br\Contracts\ContractComposition", mappedBy="contract")
     */
    private $sections;

    /**
     * @var \DateTime
     *
     * @Column(type="datetime")
     */
    private $date;

    /**
     * @ManyToOne(targetEntity="Litus\Entity\Users\Person", fetch="LAZY")
     * @JoinColumn(name="author", referencedColumnName="id", nullable="false")
     *
     * @var Person
     */
    private $author;

    /**
     * @ManyToOne(targetEntity="Litus\Entity\Users\People\Company", fetch="LAZY")
     * @JoinColumn(name="company", referencedColumnName="id", nullable="false")
     *
     * @var Company
     */
    private $company;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->parts = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getParts()
    {
        return $this->parts;
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
     * @param \Litus\Entity\Users\Person $author
     * @return void
     */
    public function setAuthor(Person $author)
    {
        if($author === null)
            throw new \InvalidArgumentException('Author cannot be null');
        $this->author = $author;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany(Company $company)
    {
        if($company === null)
            throw new \InvalidArgumentException('Company cannot be null');
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
