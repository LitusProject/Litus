<?php

namespace CudiBundle\Entity\Sale\Article\Restriction;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Restriction\Amount")
 * @ORM\Table(name="cudi_sale_articles_restrictions_amount")
 */
class Amount extends \CudiBundle\Entity\Sale\Article\Restriction
{
    /**
     * @var integer The value of the restriction
     *
     * @ORM\Column(type="smallint")
     */
    private $value;

    /**
     * @param Article $article The article of the restriction
     * @param integer $value   The value of the restriction
     */
    public function __construct(Article $article, $value)
    {
        parent::__construct($article);

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'amount';
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return (string) $this->value;
    }

    /**
     * @param Person        $person
     * @param EntityManager $entityManager
     *
     * @return boolean
     */
    public function canBook(Person $person, EntityManager $entityManager)
    {
        $academicYear = AcademicYear::getUniversityYear($entityManager);

        $bookings = $entityManager
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                $this->getArticle(),
                $person,
                $academicYear
            );
        $amount = 0;
        foreach ($bookings as $booking) {
            $amount += $booking->getNumber();
        }

        return $amount < $this->value;
    }
}
