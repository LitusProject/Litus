<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Entity\Sale\Article;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Restriction")
 * @ORM\Table(name="cudi.sales_articles_restrictions")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "amount"="CudiBundle\Entity\Sale\Article\Restriction\Amount",
 *      "member"="CudiBundle\Entity\Sale\Article\Restriction\Member",
 *      "study"="CudiBundle\Entity\Sale\Article\Restriction\Study"
 * })
 */
abstract class Restriction
{
    /**
     * @var integer The ID of the restriction
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Article The article of the restriction
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article", inversedBy="barcodes")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @param Article $article The article of the restriction
     */
    public function __construct(Article $article)
    {
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
     * @return
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return string
     */
    abstract public function getValue();

    /**
     * @param Person        $person
     * @param EntityManager $entityManager
     *
     * @return boolean
     */
    abstract public function canBook(Person $person, EntityManager $entityManager);
}
