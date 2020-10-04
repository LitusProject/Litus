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
     * @var string status of this Article-request in this order
     *
     * @ORM\Column(name="status", type="text")
     */
    private $status;

    /**
     * @static
     * @var array All the possible statuses allowed
     */
    public static $POSSIBLE_STATUSES = array(
        'okay' => 'Okay',
        'lost' => 'Lost',
        'broken' => 'Broken',
        'dirty' => 'Dirty',
        'none' => 'None',
//        TODO: vragen aan Sietze
    );

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
        $this->status = 'None';
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

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status?OrderArticleMap::$POSSIBLE_STATUSES[$this->status]:"None";
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
