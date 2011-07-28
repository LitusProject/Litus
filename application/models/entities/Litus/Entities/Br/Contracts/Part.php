<?php

namespace Litus\Entities\Br\Contracts;

use Litus\Entities\Users\Person;

/**
 * @Entity(repositoryClass="Litus\Repositories\Br\Contracts\PartRepository")
 * @Table(name="br.contract_section")
 */
class Section {

    /**
	 * @Id
     * @Column(type="string")
     *
     * @var string
     */
    private $name;

    /**
     *
     * @Column(type="string", length=65536, nullable="false")
     *
     * @var string
     */
    private $content;

    /**
     * @ManyToOne(targetEntitiy="Litus\Entities\Users\Person", fetch="LAZY")
     * @JoinColumn(name="author_id", referencedColumnName="id", onUpdate="CASCADE", nullable="false")
     *
     * @var Person
     */
    private $author;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if($name === null)
            throw new \Exception("Can't have a null name.");
        $this->name = $name;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        if($text === null)
            throw new \Exception("Can't have a null text");
        $this->text = $text;
    }
}
