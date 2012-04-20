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
 
namespace CudiBundle\Entity\Articles;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Articles\MetaInfo,
	CudiBundle\Entity\Stock\StockItem,
	CudiBundle\Entity\Supplier,
	Doctrine\ORM\EntityManager;

/**
 * @MappedSuperclass
 */
abstract class Stock extends \CudiBundle\Entity\Article
{
    /**
     * @var integer The purchase price of the article
     *
     * @Column(name="purchase_price", type="bigint")
     */
    private $purchasePrice;

    /**
     * @var integer The sell price of the article
     *
     * @Column(name="sell_price", type="bigint")
     */
    private $sellPrice;

    /**
     * @var integer The barcode of the article
     *
     * @Column(type="bigint", nullable=true)
     */
    private $barcode;

    /**
     * @var boolean Flag whether the aritcle is bookable
     *
     * @Column(type="boolean")
     */
    private $bookable;

    /**
     * @var boolean Flag whether the aritcle is unbookable
     *
     * @Column(type="boolean")
     */
    private $unbookable;

    /**
     * @var \CudiBundle\Entity\Supplier The supplier of the article
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

	/**
	 * @var boolean Flag whether the aritcle can expire
	 *
	 * @Column(type="boolean")
	 */
	private $canExpire;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param string $title The title of the article
     * @param \CudiBundle\Entity\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
     * @param float $purchasePrice The purchase price of this article.
	 * @param float $sellPrice The sell price of this article.
     * @param integer $barcode This article's barcode.
     * @param boolean $bookable Indicates whether the article can be booked.
     * @param boolean $unbookable Indicates whether the article can be unbooked.
     * @param \CudiBundle\Entity\Supplier $supplier The supplier of the stock item.
     * @param boolean $canExpire Whether the article can expire.
     */
    public function __construct(EntityManager $entityManager, $title, MetaInfo $metaInfo, $purchasePrice, $sellPrice, $barcode, $bookable = false, $unbookable = false, Supplier $supplier = null, $canExpire = false)
    {
        parent::__construct($title, $metaInfo);

        $this->setPurchasePrice($purchasePrice)
			->setSellPrice($sellPrice)
			->setBarcode($barcode)
			->setSupplier($supplier)
			->setIsBookable($bookable)
			->setIsUnbookable($unbookable)
			->setCanExpire($canExpire);
			
		$stockItem = new StockItem($this);
		$entityManager->persist($stockItem);
    }

	/**
	 * @return integer
	 */
	public function getPurchasePrice()
	{
		return $this->purchasePrice;
	}
	
	/**
     * @param float $purchasePrice
	 *
     * @return \CudiBundle\Entity\Articles\Stock
     */
	public function setPurchasePrice($purchasePrice)
	{
		$this->purchasePrice = $purchasePrice*100;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getSellPrice()
	{
		return $this->sellPrice;
	}
	
	/**
	 * @param \CommonBundle\Entity\Users\Person
	 *
	 * @return integer
	 */
	public function getSellPriceForPerson(EntityManager $entityManager, Person $person)
	{
	    if ($person->isMember()) {
	        $type = $entityManager->getRepository('CudiBundle\Entity\Articles\Discount\Type')
	            ->findOneByName('member');
	            
	        $discount = $entityManager->getRepository('CudiBundle\Entity\Articles\Discount\Discount')
	            ->findOneByArticleAndType($this, $type);
	        
	        if (null == $discount)
	            return $this->sellPrice;
	        
	        return $discount->getArticlePrice();
	    }
	    return $this->sellPrice;
	}
	
	/**
     * @param float $sellPrice
	 *
     * @return \CudiBundle\Entity\Articles\Stock
     */
	public function setSellPrice($sellPrice)
	{
		$this->sellPrice = $sellPrice*100;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getBarcode()
	{
		return $this->barcode;
	}
	
	/**
     * @param integer $barcode
	 *
     * @return \CudiBundle\Entity\Articles\Stock
     */
	public function setBarcode($barcode)
	{
	    if (null === $barcode) {
	        $this->barcode = null;
	        return $this;
	    }
	    
	    if (strlen($barcode) == 13)
	        $barcode = floor($barcode / 10);
	    
	    if (strlen($barcode) != 12)
	        throw new \InvalidArgumentException('Invalid barcode given');
	    
		$this->barcode = $barcode;
		return $this;
	}
	
	/**
	 * @return \CudiBundle\Entity\Suppplier
	 */
	public function getSupplier()
	{
		return $this->supplier;
	}
	
	/**
     * @param \CudiBundle\Entity\Supplier $supplier
	 *
     * @return \CudiBundle\Entity\Articles\Stock
     */
	public function setSupplier(Supplier $supplier = null)
	{
		$this->supplier = $supplier;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function canExpire()
	{
		return $this->canExpire;
	}
	
	/**
     * @param boolean $canExpire
	 *
     * @return CudiBundle\Entity\Articles\Stock
     */
	public function setCanExpire($canExpire)
	{
		$this->canExpire = $canExpire;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isBookable()
	{
		return $this->bookable;
	}
	
	/**
     * @param boolean $bookable
	 *
     * @return \CudiBundle\Entity\Articles\Stock
     */
	public function setIsBookable($bookable)
	{
		$this->bookable = $bookable;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isUnbookable()
	{
		return $this->unbookable;
	}
	
	/**
     * @param boolean $unbookable
	 *
     * @return \CudiBundle\Entity\Articles\Stock
     */
	public function setIsUnbookable($unbookable)
	{
		$this->unbookable = $unbookable;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isStock()
	{
		return true;
	}
}