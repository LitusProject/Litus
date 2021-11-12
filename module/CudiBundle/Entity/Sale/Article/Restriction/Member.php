<?php

namespace CudiBundle\Entity\Sale\Article\Restriction;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\User\Person;
use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Restriction\Member")
 * @ORM\Table(name="cudi_sale_articles_restrictions_member")
 */
class Member extends \CudiBundle\Entity\Sale\Article\Restriction
{
    /**
     * @var boolean The value of the restriction
     *
     * @ORM\Column(type="boolean")
     */
    private $value;

    /**
     * @param Article $article The article of the restriction
     * @param boolean $value   The value of the restriction
     */
    public function __construct(Article $article, $value)
    {
        parent::__construct($article);

        $this->value = !!$value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'member';
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value ? 'Yes' : 'No';
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
            ->findAllOpenByPerson($person);

        $membershipArticle = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $organization = null;
        if ($person instanceof Academic) {
            $organization = $person->getOrganization($academicYear);
        }

        $membershipBooked = false;
        if ($organization !== null && isset($membershipArticle[$organization->getId()])) {
            foreach ($bookings as $booking) {
                // TODO: Remove all bookings that can no longer be booked on cancellation of membership

                if ($booking->getArticle()->getId() == $membershipArticle[$organization->getId()]) {
                    $membershipBooked = true;
                    break;
                }
            }
        }

        return $this->value === ($person->isMember($academicYear) || $membershipBooked);
    }
}
