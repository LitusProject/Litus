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

namespace CudiBundle\Entity\Sale\SaleItem;

use CudiBundle\Entity\Sale\Article,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\SaleItem\Prof")
 */
class Prof extends \CudiBundle\Entity\Sale\SaleItem
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $person;

    /**
     * @param \CudiBundle\Entity\Sale\Article $article
     * @param integer $number
     * @param string $person
     * @param \Doctrine\ORM\EntityManager|null $entityManager
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