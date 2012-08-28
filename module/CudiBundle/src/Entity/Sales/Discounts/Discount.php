<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sales\Discounts;

use CudiBundle\Entity\Sales\Article as Article,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Discounts\Discount")
 * @ORM\Table(name="cudi.sales_discounts_discounts")
 */
class Discount
{
    /**
     * @var integer The ID of the discount
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CudiBundle\Entity\Sales\Discounts\Template The template of the discount
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Discounts\Template")
     * @ORM\JoinColumn(name="template", referencedColumnName="id")
     */
    private $template;

    /**
     * @var integer The value of the discount
     *
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $value;

    /**
     * @var string The method of this discount (percentage, fixed, override)
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $method;

    /**
     * @var string The type of discount (member, acco)
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @var \CudiBundle\Entity\Sales\Article The article of the discount
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var array The possible types of a discount
     */
    private static $POSSIBLE_TYPES = array(
        'member', 'acco'
    );

    /**
     * @var array The possible methods of a discount
     */
    private static $POSSIBLE_METHODS = array(
        'percentage', 'fixed', 'override'
    );

    /**
     * @param \CudiBundle\Entity\Sales\Article The article of the discount
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @param \CudiBundle\Entity\Sales\Discounts\Template The template of the discount
     *
     * @return \CudiBundle\Entity\Sales\Discounts\Discount
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
        $this->value = null;
        $this->method = null;
        $this->type = null;
        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @param integer The value of the discount
     * @param string The method of the discount
     * @param string The type of the discount
     *
     * @return \CudiBundle\Entity\Sales\Discounts\Discount
     */
    public function setDiscount($value, $method, $type)
    {
        if (!self::isValidDiscountType($type))
            throw new \InvalidArgumentException('The discount type is not valid.');

        if (!self::isValidDiscountMethod($method))
            throw new \InvalidArgumentException('The discount method is not valid.');

        $this->template = null;
        $this->value = $value * 100;
        $this->method = $method;
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public static function isValidDiscountType($type)
    {
        return in_array($type, self::$POSSIBLE_TYPES);
    }

    /**
     * @return boolean
     */
    public static function isValidDiscountMethod($method)
    {
        return in_array($method, self::$POSSIBLE_METHODS);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Discounts\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return integer
     */
    public function getValue()
    {
        if (!isset($this->value))
            return $this->template->getValue();
        return $this->value;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        if (!isset($this->method))
            return $this->template->getMethod();
        return $this->method;
    }

    /**
     * @return string
     */
    public function getType()
    {
        if (!isset($this->type))
            return $this->template->getType();
        return $this->type;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Articl
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param integer $price
     *
     * @return integer
     */
    public function apply($price)
    {
        switch ($this->getMethod()) {
            case 'percentage':
                return round($price * (10000 - $this->getValue()) / 10000);
            case 'fixed':
                return $price - $this->getValue();
            case 'override':
                return $this->getValue();
        }
    }
}
