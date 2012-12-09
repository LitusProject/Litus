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

namespace CudiBundle\Entity\Stock\Orders;

use CudiBundle\Entity\Sales\Article,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Orders\Virtual")
 * @ORM\Table(name="cudi.stock_orders_virtual")
 */
class Virtual
{
    /**
     * @var integer The ID of the item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CudiBundle\Entity\Sales\Article The article of the item
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var integer The number of items ordered
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var \DateTime The time the order was created
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @param \CudiBundle\Entity\Sales\Article $article The article of the item
     * @param integer $number The number of items ordered
     */
    public function __construct(Article $article, $number)
    {
        $this->article = $article;
        $this->number = $number;
        $this->dateCreated = new DateTime;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }
}
