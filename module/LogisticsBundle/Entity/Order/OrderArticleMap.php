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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Entity\Order;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Article;
use LogisticsBundle\Entity\Order;
use SyllabusBundle\Entity\Subject;

/**
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Order\OrderArticleMap")
 * @ORM\Table(name="logistics_order_order_article_map")
 */
class OrderArticleMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Order The Order of the mapping
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Order")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $order;

    /**
     * @var Article The Article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Article")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $article;

    /**
     * @var integer amount of this Article in this order
     *
     * @ORM\Column(type="bigint")
     */
    private $amount;

    /**
     * OrderArticleMap constructor.
     * @param Order $order
     * @param Article $article
     * @param integer $amount
     */
    public function __construct(Order $order, Article $article, $amount)
    {
        $this->order = $order;
        $this->article = $article;
        $this->amount = $amount;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param integer $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

}
