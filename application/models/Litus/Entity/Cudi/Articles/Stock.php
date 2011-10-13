<?php

namespace Litus\Entity\Cudi\Articles;

/**
 * @MappedSuperclass
 */
abstract class Stock extends \Litus\Entity\Cudi\Article
{
    /**
     * @Column(name="purchase_price", type="bigint")
     */
    private $purchasePrice;

    /**
     * @Column(name="sell_price", type="bigint")
     */
    private $sellPrice;

    /**
     * @Column(name="sell_price_members", type="bigint")
     */
    private $sellPriceMembers;

    /**
     * @Column(type="smallint")
     */
    private $barcode;

    /**
     * @Column(type="boolean")
     */
    private $bookable;

    /**
     * @Column(type="boolean")
     */
    private $unbookable;

    /**
     * @ManyToOne(targetEntity="Litus\Entity\Cudi\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

	/**
	 * @Column(type="boolean")
	 */
	private $canExpire;

    /**
     * @param string $title The title of the article
     * @param Litus\Entity\Cudi\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
     * @param bigint $purchase_price The purchase price of this article.
     * @param bigint $sellPrice The sell price of this article for non-members.
     * @param bigint $sellPriceMembers The sell price of this article for members.
     * @param smallint $barcode This article's barcode.
     * @param boolean $bookable Indicates whether the article can be booked.
     * @param boolean $unbookable Indicates whether the article can be unbooked.
     * @param Litus\Entity\Cudi\Supplier $supplier The supplier of the stock item.
     */
    public function __construct($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable, $supplier, $canExpire)
    {
        parent::__construct($title, $metaInfo);

        $this->purchasePrice = $purchase_price*100;
        $this->sellPrice = $sellPrice*100;
        $this->sellPriceMembers = $sellPriceMembers*100;
        $this->barcode = $barcode;
        $this->bookable = $bookable;
        $this->unbookable = $unbookable;
		$this->supplier = $supplier;
		$this->canExpire = $canExpire;
    }

	public function canExpire()
	{
		return $this->canExpire;
	}
	
	public function isBookable()
	{
		return $this->bookable;
	}
}