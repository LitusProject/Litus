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

namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Sales\Article,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Stock\Retour")
 * @ORM\Table(
 *    name="cudi.stock_retours",
 *    indexes={@ORM\Index(name="stock_retours_time", columns={"timestamp"})}
 * )
 */
class Retour
{
    /**
     * @var integer The ID of the retour
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The time the retour item was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var integer The number of the retour
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var \CudiBundle\Entity\Sales\Article The article of the retour
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sales\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var \CommonBundle\Entity\Users\Person The person of the retour
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The comment of the retour
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @param \CudiBundle\Entity\Sales\Article $article The article of the retour
     * @param integer $number The number of the retour
     * @param \CommonBundle\Entity\Users\Person $person The person of the retour
     * @param string $comment The comment of the retour
     */
    public function __construct(Article $article, $number, Person $person, $comment)
    {
        $this->article = $article;
        $this->person = $person;
        $this->number = $number;
        $this->comment = $comment;
        $this->timestamp = new DateTime();
        $article->addStockValue(-$number);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return \CommonBundle\Entity\Users\person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return integer
     */
    public function getPrice()
    {
        return $this->article->getPurchasePrice() * $this->number;
    }
}
