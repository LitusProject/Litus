<?php

namespace Litus\Entities\Br\Contracts;

use Litus\Entities\Users\Person;
use Litus\Util\AcademicYear;

/**
 * A section represents a part of a Contract.
 *
 * @Entity(repositoryClass="Litus\Repositories\Br\Contracts\SectionRepository")
 * @Table(name="br.contract_section")
 */
class Section {
    /**
     * The name of this Section.
     *
	 * @Id
     * @Column(type="string")
     *
     * @var string
     */
    private $name;

    /**
     * The content of this Contract Section.
     *
     * @Column(type="string", length=65536, nullable="false")
     *
     * @var string
     */
    private $content;

    /**
     * The Author of this Contract Section.
     *
     * @ManyToOne(targetEntity="Litus\Entities\Users\Person", fetch="LAZY")
     * @JoinColumn(name="author", referencedColumnName="id", nullable="false")
     *
     * @var Person
     */
    private $author;

    /**
     * The Academic year in which this Contract Section was written.
     *
     * @Column(type="string", length=4)
     *
     * @var string
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
            throw new \Exception('Can\'t have a null name.');
        $this->name = $name;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        if($text === null)
            throw new \Exception('Can\'t have a null text');
        $this->text = $text;
    }

    public function setAuthor(Person $author)
    {
        if($author == null)
            throw new \Exception('Can\'t have null as author');
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
