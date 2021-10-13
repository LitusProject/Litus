<?php

namespace CudiBundle\Entity\Article\Option;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Article\Option\Binding")
 * @ORM\Table(name="cudi_articles_options_bindings")
 */
class Binding
{
    /**
     * @var integer The ID of the binding
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the binding
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The code of the binding
     *
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @param string $name
     * @param string $code
     */
    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
