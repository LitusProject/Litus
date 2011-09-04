<?php

namespace Litus\Entity\Config;

/**
 *
 * @Entity(repositoryClass="Litus\Repository\Config\Config")
 * @Table(name="public.config")
 */
class Config {

    /**
     * @var string
     *
     * @Id
     * @Column(type="string")
     */
    private $key;

    /**
     * @var string
     *
     * @Column(type="string", nullable="false")
     */
    private $value;

    /**
     * @var string
     *
     * @Column(type="string", nullable="true")
     */
    private $description;

    public function __construct($key)
    {
        if(!is_string($key))
            throw new \InvalidArgumentException('Key must be a string.');
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        if(!is_string($value))
            throw new \InvalidArgumentException('Value must be a string.');
        $this->value = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description = null)
    {
        if(($description !== null) && !is_string($description))
            throw new InvalidArgumentException('Description must be a string or null');
        $this->description = $description;
    }
}
