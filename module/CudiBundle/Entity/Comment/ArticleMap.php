<?php

namespace CudiBundle\Entity\Comment;

use CudiBundle\Entity\Article;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Comment\ArticleMap")
 * @ORM\Table(name="cudi_comments_articles_map")
 */
class ArticleMap
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
     * @var Article The article of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Comment The comment of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Comment\Comment")
     * @ORM\JoinColumn(name="comment", referencedColumnName="id")
     */
    private $comment;

    /**
     * @param Article $article
     * @param Comment $comment
     */
    public function __construct(Article $article, Comment $comment)
    {
        $this->article = $article;
        $this->comment = $comment;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }
}
