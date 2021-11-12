<?php

namespace CudiBundle\Entity\Article;

use CudiBundle\Entity\Article;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Article\History")
 * @ORM\Table(name="cudi_articles_history")
 */
class History
{
    /**
     * @var integer The ID of this article history
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Article The newest version of the two
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Article The oldest version of the two
     *
     * @ORM\OneToOne(targetEntity="CudiBundle\Entity\Article", cascade={"persist"})
     * @ORM\JoinColumn(name="precursor", referencedColumnName="id")
     */
    private $precursor;

    /**
     * @param Article      $article   The new version of the article
     * @param Article|null $precursor The old version of the article
     */
    public function __construct(Article $article, Article $precursor = null)
    {
        $this->precursor = $precursor ?? clone $article;

        $this->precursor->setVersionNumber($article->getVersionNumber())
            ->setIsHistory(true);

        if ($this->precursor->getTimeStamp() > $article->getTimeStamp()) {
            $date = $article->getTimeStamp();
            $article->setTimeStamp($this->precursor->getTimeStamp());
            $this->precursor->setTimeStamp($date);
        }

        $article->setVersionNumber($article->getVersionNumber() + 1);

        $this->article = $article;
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
     * @param Article $article
     *
     * @return self
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return Article
     */
    public function getPrecursor()
    {
        return $this->precursor;
    }
}
