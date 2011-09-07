<?php

namespace Litus\Entity\Br\Contracts;

use \Doctrine\Common\Collections\ArrayCollection;

use \Litus\Entity\Users\Person;
use \Litus\Entity\Users\People\Company;

/**
 *
 * @Entity(repositoryClass="Litus\Repository\Br\Contracts\Contract")
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
     * @var \DateTime The date and time when this contract was written
     *
     * @Column(type="datetime")
     */
    private $date;

    /**
     * @var \Litus\Entity\Users\Person The author of this contract
     *
     * @ManyToOne(targetEntity="Litus\Entity\Users\Person", fetch="LAZY")
     * @JoinColumn(name="author", referencedColumnName="id", nullable="false")
     */
    private $author;

    /**
     * @var \Litus\Entity\Users\People\Company The company for which this contract is meant
     *
     * @ManyToOne(targetEntity="Litus\Entity\Users\People\Company", fetch="LAZY")
     * @JoinColumn(name="company", referencedColumnName="id", nullable="false")
     */
    private $company;

    /**
     * @var \Litus\Entity\Br\Contracts\ContractComposition The sections this contract contains
     *
     * @OneToMany(targetEntity="Litus\Entity\Br\Contracts\ContractComposition", mappedBy="contract")
     * @OrderBy({"order" = "ASC"})
     */
    private $composition;

    /**
     * @var int The discount the company gets, in %.
     *
     * @Column(type="integer")
     */
    private $discount;

    /**
     * @var string The title of the contract
     *
     * @Column(type="string", nullable="false")
     */
    private $title;

    /**
     * @param \Litus\Entity\Users\Person $author The author of this contract
     * @param \Litus\Entity\Users\People\Company $company The company for which this contract is meant
     * @param int $discount The discount associated with this contract
     * @param string $title The title of the contract
     */
    public function __construct(Person $author, Company $company, $discount, $title)
    {
        $this->setDate();
        $this->setAuthor($author);
        $this->setCompany($company);
        $this->setDiscount($discount);
        $this->setTitle($title);

        $this->sections = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return \Litus\Entity\Br\Contracts\Contract
     */
    public function setDate()
    {
        $this->date = new \DateTime();

        return $this;
    }

    /**
     * @return \Litus\Entity\Users\Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @throws \InvalidArgumentException
     * @param \Litus\Entity\Users\Person $author
     * @return \Litus\Entity\Br\Contracts\Contract
     */
    public function setAuthor(Person $author)
    {
        if ($author === null)
            throw new \InvalidArgumentException('Author cannot be null.');
        $this->author = $author;

        return $this;
    }

    /**
     * @return \Litus\Entity\Users\People\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @throws \InvalidArgumentException
     * @param \Litus\Entity\Users\People\Company $company
     * @return \Litus\Entity\Br\Contracts\Contract
     */
    public function setCompany(Company $company)
    {
        if ($company === null)
            throw new \InvalidArgumentException('Company cannot be null.');
        $this->company = $company;

        return $this;
    }


    /**
     * @return array
     */
    public function getParts()
    {
        return $this->sections->toArray();
    }

    /**
     * @param \Litus\Entity\Br\Contracts\Section $section The section that should be added
     * @param int $position The position of this section
     * @return \Litus\Entity\Br\Contracts\Contract
     */
    public function addSection(Section $section, $position)
    {
        $this->sections->add(
            new ContractComposition($this, $section, $position)
        );

        return $this;
    }

    /**
     * @param array $sections The array containing all sections that should be added.
     *                        The array keys will be used as the position.
     * @return \Litus\Entity\Br\Contracts\Contract
     */
    public function addSections(array $sections)
    {
        foreach ($sections as $position => $section)
            $this->addSection($section, $position);

        return $this;
    }

    /**
     * @throws \InvalidArgumentException if $discount is invalid
     * @param int $discount the discount, $discount >= 0 && $discount <= 100
     * @return \Litus\Entity\Br\Contracts\Contract
     */
    public function setDiscount($discount)
    {
        if (!is_integer($discount) || ($discount < 0) || ($discount > 100))
            throw new \InvalidArgumentException('Invalid discount');
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $title The title of the contract
     * @return \Litus\Entity\Br\Contracts\Contract
     */
    public function setTitle($title)
    {
        if (($title === null) || !is_string($title))
            throw new \InvalidArgumentException('Invalid title');
        $this->title = $title;

        return $this;
    }
}
