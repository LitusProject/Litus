<?php

namespace Litus\Entity\Br\Contracts;

use \Litus\Entity\Users\Person;
use \Litus\Util\AcademicYear;

use \InvalidArgumentException;

/**
 * A section represents a part of a Contract.
 *
 * @Entity(repositoryClass="Litus\Repository\Br\Contracts\SectionRepository")
 * @Table(name="br.contract_section")
 */
class Section {
    /**
     * @var string The name of this Section.
     *
	 * @Id
     * @Column(type="string")
     */
    private $name;

    /**
     * @var string The content of this Contract Section.
     *
     * @Column(type="string", length=65536, nullable="false")
     */
    private $content;

    /**
     * @var Person The Author of this Contract Section.
     *
     * @ManyToOne(targetEntity="Litus\Entity\Users\Person", fetch="LAZY")
     * @JoinColumn(name="author", referencedColumnName="id", nullable="false")
     */
    private $author;

    /**
     * @var string The Academic year in which this Contract Section was written.
     *
     * @Column(type="string", length=4)
     */
    private $year;

    public function __construct()
    {
        $this->year = AcademicYear::getAcademicYear();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if($name === null)
            throw new InvalidArgumentException('Can\'t have a null name.');
        $this->name = $name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($text)
    {
        if($text === null)
            throw new InvalidArgumentException('Can\'t have a null text');
        $this->content = $text;
    }

    public function setAuthor(Person $author)
    {
        if($author == null)
            throw new InvalidArgumentException('Can\'t have null as author');
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getYear()
    {
        return $this->year;
    }
}
