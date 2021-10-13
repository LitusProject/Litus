<?php

namespace CudiBundle\Entity\Sale\SaleItem;

use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\SaleItem\Prof")
 */
class Prof extends \CudiBundle\Entity\Sale\SaleItem
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $person;

    /**
     * @param Article            $article
     * @param integer            $number
     * @param string             $person
     * @param EntityManager|null $entityManager
     */
    public function __construct(Article $article, $number, $person, EntityManager $entityManager = null)
    {
        parent::__construct($article, $number, 0, null, null, $entityManager);

        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'prof';
    }
}
