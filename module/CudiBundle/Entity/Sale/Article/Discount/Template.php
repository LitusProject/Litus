<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sale\Article\Discount;

use CommonBundle\Entity\General\Organization,
    CudiBundle\Entity\Sale\Article,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Discount\Template")
 * @ORM\Table(name="cudi.sales_articles_discounts_templates")
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
     * @var boolean Apply the discount only once
     *
     * @ORM\Column(name="apply_once", type="boolean")
     */
    private $applyOnce;

    /**
     * @var Organization|null The organization for the discount
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization")
     * @ORM\JoinColumn(name="organization", referencedColumnName="id", nullable=true)
     */
    private $organization;

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
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param  int  $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = (int) (str_replace(',', '.', $value) * 100);

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param  string $method
     * @return self
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return Discount::$possibleTypes[$this->type];
    }

    /**
     * @param  string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Organization|null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param  Organization|null $organization
     * @return self
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return string
     */
    public function getRawType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRounding()
    {
        return Discount::$possibleRoundings[$this->rounding]['name'];
    }

    /**
     * @param  string $rounding
     * @return self
     */
    public function setRounding($rounding)
    {
        $this->rounding = $rounding;

        return $this;
    }

    /**
     * @return boolean
     */
    public function applyOnce()
    {
        return $this->applyOnce;
    }

    /**
     * @param  boolean $applyOnce
     * @return self
     */
    public function setApplyOnce($applyOnce)
    {
        $this->applyOnce = $applyOnce;

        return $this;
    }
}
