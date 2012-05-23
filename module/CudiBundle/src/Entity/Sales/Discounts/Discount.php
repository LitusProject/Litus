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

use CudiBundle\Entity\Sales\Article as Article;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\Discounts\Discount")
 * @Table(name="cudi.sales_discounts_discount")
 */
class Discount
{
    /**
     * @var integer The ID of the discount
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var \CudiBundle\Entity\Sales\Discounts\Template The template of the discount
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Discounts\Template")
     * @JoinColumn(name="template", referencedColumnName="id")
     */
    private $template;
    
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
     * @var \CudiBundle\Entity\Sales\Article The article of the discount
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
     * @JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;
    
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
     * @param integer The value of the discount
     * @param string The method of the discount
     * @param string The type of the discount
     *
     * @return \CudiBundle\Entity\Sales\Discounts\Discount
     */
    public function setDiscount($value, $method, $type)
    {
        $this->value = $value;
        $this->method = $method;
        $this->type = $type;
        return $this;
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
        if (isset($this->type) && $this->type != 'override')
            return $this->template->getValue();
        return $this->value;
    }
    
    /**
     * @return string
     */
    public function getMethod()
    {
        if (isset($this->type) && $this->type != 'override')
            return $this->template->getMethod();
        return $this->method;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        if (isset($this->type))
            return $this->type;
        return $this->template->getType();
    }
    
    /**
     * @return \CudiBundle\Entity\Sales\Articl
     */
    public function getArticle()
    {
        return $this->article;
    }
}