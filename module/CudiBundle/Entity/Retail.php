<?php

namespace CudiBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\Article;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Retail")
 * @ORM\Table(name="cudi_retail")
 */
class Retail
{
    /**
     * @var integer The ID of this retail article
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Article The article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Academic The owner of the article
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     *
     */
    private $owner;

    /**
     * @var integer The price of this article
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var boolean The flag whether the owner wants to be anonymous or not
     *
     * @ORM\Column(name="is_anonymous", type="boolean")
     */
    private $isAnonymous;

    /**
     * @var string Extra information
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function __construct(Article $article, Academic $owner)
    {
        $this->owner = $owner;
        $this->article = $article;
        $this->price = 0;
        $this->isAnonymous = false;
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \CudiBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return string | null
     */
    public function getVisibleOwnerName(): string
    {
        if ($this->isAnonymous) {
            return 'Anonymous';
        } else {
            return $this->owner->getFullName();
        }
    }

    /**
     * @return Academic
     */
    public function getOwner(): Academic
    {
        return $this->owner;
    }

    /**
     * @return
     */
    public function getPrice()
    {
        return number_format($this->price / 100, 2);
    }

    /**
     * @param integer $price
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * @return boolean
     */
    public function isAnonymous(): bool
    {
        return $this->isAnonymous;
    }

    /**
     * @param boolean $isAnonymous
     */
    public function setAnonymous(bool $isAnonymous)
    {
        $this->isAnonymous = $isAnonymous;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment ?? '';
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }
}
