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
     * @var string The type of rounding
     *
     * @ORM\Column(type="string")
     */
    private $rounding;

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
    public static $POSSIBLE_TYPES = array(
        'member' => 'Member',
        'acco' => 'Acco',
    );

    /**
     * @var array The possible methods of a discount
     */
    public static $POSSIBLE_METHODS = array(
        'percentage' => 'Percentage',
        'fixed' => 'Fixed',
        'override' => 'Override',
    );

    /**
     * @var array The possible methods of rounding
     */
    public static $POSSIBLE_ROUNDINGS = array(
        'none' => array(
            'name' => 'None',
            'value' => '1',
            'type' => 'up',
        ),
        '0.05_up' => array(
            'name' => '0.05 (up)',
            'value' => '5',
            'type' => 'up',
        ),
        '0.05_down' => array(
            'name' => '0.05 (down)',
            'value' => '5',
            'type' => 'down',
        ),
        '0.10_up' => array(
            'name' => '0.10 (up)',
            'value' => '10',
            'type' => 'up',
        ),
        '0.10_down' => array(
            'name' => '0.10 (down)',
            'value' => '10',
            'type' => 'down',
        ),
        '0.50_up' => array(
            'name' => '0.50 (up)',
            'value' => '50',
            'type' => 'up',
        ),
        '0.50_down' => array(
            'name' => '0.50 (down)',
            'value' => '50',
            'type' => 'down',
        ),
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
     * @param string The type of the rounding
     *
     * @return \CudiBundle\Entity\Sales\Discounts\Discount
     */
    public function setDiscount($value, $method, $type, $rounding)
    {
        if (!self::isValidDiscountType($type))
            throw new \InvalidArgumentException('The discount type is not valid.');

        if (!self::isValidDiscountMethod($method))
            throw new \InvalidArgumentException('The discount method is not valid.');

        if (!self::isValidRoundingType($rounding))
            throw new \InvalidArgumentException('The rounding type is not valid.');

        $this->template = null;
        $this->value = $value * 100;
        $this->method = $method;
        $this->type = $type;
        $this->rounding = $rounding;
        return $this;
    }

    /**
     * @return boolean
     */
    public static function isValidDiscountType($type)
    {
        return array_key_exists($type, self::$POSSIBLE_TYPES);
    }

    /**
     * @return boolean
     */
    public static function isValidDiscountMethod($method)
    {
        return array_key_exists($method, self::$POSSIBLE_METHODS);
    }

    /**
     * @return boolean
     */
    public static function isValidRoundingType($rounding)
    {
        return array_key_exists($rounding, self::$POSSIBLE_ROUNDINGS);
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
        return self::$POSSIBLE_TYPES[$this->type];
    }

    /**
     * @return string
     */
    public function getRawType()
    {
        if (!isset($this->type))
            return $this->template->getRawType();
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
     * @return string
     */
    public function getRounding()
    {
        if (!isset($this->rounding) && isset($this->template))
            return $this->template->getRounding();
        else if (isset($this->rounding))
            return self::$POSSIBLE_ROUNDINGS[$this->rounding]['name'];
    }

    /**
     * @param integer $price
     *
     * @return integer
     */
    public function apply($price)
    {
        $value = 0;
        switch ($this->getMethod()) {
            case 'percentage':
                $value = round($price * (10000 - $this->getValue()) / 10000);
                break;
            case 'fixed':
                $value = $price - $this->getValue();
                break;
            case 'override':
                $value = $this->getValue();
                break;
        }

        if ($this->rounding) {
            $rounding = self::$POSSIBLE_ROUNDINGS[$this->rounding];

            if ($rounding['type'] == 'up') {
                $value = ceil($value/$rounding['value'])*$rounding['value'];
            } else {
                $value = floor($value/$rounding['value'])*$rounding['value'];
            }
        }

        return $value;
    }
}
