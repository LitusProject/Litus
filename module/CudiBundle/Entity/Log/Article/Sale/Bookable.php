<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Entity\Log\Article\Sale;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Articles\Sales\Bookable")
 * @ORM\Table(name="cudi.log_articles_sales_bookable")
 */
class Bookable extends \CudiBundle\Entity\Log
{
    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \CudiBundle\Entity\Sale\Article $article
     */
    public function __construct(Person $person, Article $article)
    {
        parent::__construct($person, $article->getId());
    }

    /**
     * @return \CudiBundle\Entity\Sale\Article
     */
    public function getArticle(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getText());
    }
}
