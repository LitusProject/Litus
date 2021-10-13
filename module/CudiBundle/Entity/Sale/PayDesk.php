<?php

namespace CudiBundle\Entity\Sale;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\PayDesk")
 * @ORM\Table(name="cudi_sale_pay_desks")
 */
class PayDesk
{
    /**
     * @var integer The ID of the paydesk
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the paydesk
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The code of the paydesk
     *
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @param string $code The code of the paydesk
     * @param string $name The name of the paydesk
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
