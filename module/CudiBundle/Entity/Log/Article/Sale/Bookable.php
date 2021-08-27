<?php

namespace CudiBundle\Entity\Log\Article\Sale;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Article\Sale\Bookable")
 * @ORM\Table(name="cudi_log_articles_sales_bookable")
 */
class Bookable extends \CudiBundle\Entity\Log
{
    /**
     * @param Person  $person
     * @param Article $article
     */
    public function __construct(Person $person, Article $article)
    {
        parent::__construct($person, $article->getId());
    }

    /**
     * @param  EntityManager $entityManager
     * @return Article
     */
    public function getArticle(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getText());
    }
}
