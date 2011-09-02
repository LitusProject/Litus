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
class Section
{
    /**
     * @var string The name of this section
     *
     * @Id
     * @Column(type="string")
     */
    private $name;

    /**
     * @var string The content of this section
     *
     * @Column(type="text")
     */
    private $content;

    /**
     * @var \Litus\Entity\Users\Person The author of this section
     *
     * @ManyToOne(targetEntity="Litus\Entity\Users\Person", fetch="LAZY")
     * @JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @var string The academic year in which this section was written
     *
     * @Column(type="string", length=9)
     */
    private $year;

    /**
     * @param string $name The name of this section
     * @param string $content The content of this section
     * @param \Litus\Entity\Users\Person $author The author of this section
     */
    public function __construct($name, $content, Person $author)
    {
        $this->name = $name;
        $this->content = $content;
        $this->author = $author;

        $this->year = AcademicYear::getAcademicYear();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name The name of this section
     * @return \Litus\Entity\Br\Contracts\Section
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

<<<<<<< HEAD
    public function getContent()
    {
        return $this->content;
    }

    public function setContent($text)
    {
        if($text === null)
            throw new InvalidArgumentException('Can\'t have a null text');
        $this->content = $text;
=======
    /**
     * @return \Litus\Entity\Users\Person
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param \Litus\Entity\Users\Person $author The author of this section
     * @return \Litus\Entity\Br\Contracts\Section
     */
    public function setAuthor(Person $author)
    {
        $this->author = $author;
        return $this;
>>>>>>> 662167eaa5df7d5fc692f7bc5952f9eab9905fa1
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content The content of this section
     * @return \Litus\Entity\Br\Contracts\Section
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }
}
