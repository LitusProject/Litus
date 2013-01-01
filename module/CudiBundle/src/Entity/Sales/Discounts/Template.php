<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sales\Discounts;

use CudiBundle\Entity\Sales\Article as Article,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Discounts\Template")
 * @ORM\Table(name="cudi.sales_discounts_templates")
 */
class Template
{
    /**
     * @var integer The ID of the template
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the discount template
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var integer The value of the discount
     *
     * @ORM\Column(type="bigint")
     */
    private $value;

    /**
     * @var string The method of this discount (percentage, fixed, override)
     *
     * @ORM\Column(type="string")
     */
    private $method;

    /**
     * @var string The type of discount (member, acco)
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string The type of rounding
     *
     * @ORM\Column(type="string")
     */
    private $rounding;

    /**
     * @param integer The value of the discount
     * @param string The method of the discount
     * @param string The type of the discount
     * @param string The type of the rounding
     */
    public function __construct($name, $value, $method, $type, $rounding)
    {
        $this->name = $name;
        $this->value = $value * 100;
        $this->method = $method;
        $this->type = $type;
        $this->rounding = $rounding;
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
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Discount::$POSSIBLE_TYPES[$this->type];
    }

    /**
     * @return string
     */
    public function getRounding()
    {
        return Discount::$POSSIBLE_ROUNDINGS[$this->rounding]['name'];
    }
}
