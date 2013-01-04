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

namespace CudiBundle\Entity\Sales\Articles;

use CudiBundle\Entity\Sales\Article as Article,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Articles\Barcode")
 * @ORM\Table(name="cudi.sales_articles_barcodes")
 */
class Barcode
{
    /**
     * @var integer The ID of the barcode
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The barcode of the article
     *
     * @ORM\Column(type="bigint")
     */
    private $barcode;

    /**
     * @var \CudiBundle\Entity\Sales\Article The article of the discount
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var boolean Flag whether this is the main barcode
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $main;

    /**
     * @param \CudiBundle\Entity\Sales\Article The article of the discount
     * @param integer $barcode The barcode of the article
     * @param boolean $main Flag whether this is the main barcode
     */
    public function __construct(Article $article, $barcode, $main = false)
    {
        $this->article = $article;
        $this->barcode = $barcode;
        $this->main = $main;
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
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param integer $barcode
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Articl
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return boolean
     */
    public function isMain()
    {
        return $this->main;
    }
}
