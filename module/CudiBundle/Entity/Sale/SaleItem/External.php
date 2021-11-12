<?php

namespace CudiBundle\Entity\Sale\SaleItem;

use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\SaleItem\External")
 */
class External extends \CudiBundle\Entity\Sale\SaleItem
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
     * @param integer            $price
     * @param string             $person
     * @param EntityManager|null $entityManager
     */
    public function __construct(Article $article, $number, $price, $person, EntityManager $entityManager = null)
    {
        parent::__construct($article, $number, $price, null, null, $entityManager);

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
        return 'external';
    }
}
