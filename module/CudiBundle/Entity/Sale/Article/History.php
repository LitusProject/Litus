<?php

namespace CudiBundle\Entity\Sale\Article;

use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\History")
 * @ORM\Table(name="cudi_sale_articles_history")
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
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Article The oldest version of the two
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article", cascade={"persist"})
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
     * @return Article
     */
    public function getPrecursor()
    {
        return $this->precursor;
    }
}
