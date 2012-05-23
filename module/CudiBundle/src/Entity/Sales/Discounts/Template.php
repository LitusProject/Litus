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

use \CudiBundle\Entity\Sales\Article as Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\Discounts\Template")
 * @Table(name="cudi.sales_discounts_template")
 */
class Template
{
    /**
     * @var integer The ID of the template
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @var integer The value of the discount
     *
     * @Column(type="bigint", nullable=true)
     */
    private $value;
    
    /**
     * @var string The method of this discount (percentage, fixed, override)
     *
     * @Column(type="string", nullable=true)
     */
    private $method;
    
    /**
     * @var string The type of discount (member, acco)
     *
     * @Column(type="string", nullable=true)
     */
    private $type;
    
    /**
     * @param integer The value of the discount
     * @param string The method of the discount
     * @param string The type of the discount
     */
    public function __construct($value, $method, $type)
    {
        $this->article = $article;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
        return $this->type;
    }
}