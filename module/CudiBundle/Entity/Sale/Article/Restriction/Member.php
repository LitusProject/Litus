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

namespace CudiBundle\Entity\Sale\Article\Restriction;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Sale\Article\Restriction,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Restriction\Member")
 * @ORM\Table(name="cudi.sales_articles_restrictions_member")
 */
class Member extends Restriction
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

        $organization = $person->getOrganization($academicYear);

        $membershipBooked = false;
        if (null !== $organization && isset($membershipArticle[$organization->getId()])) {
            foreach ($bookings as $booking) {
                // TODO on cancellation of membership: remove all bookings that can no longer be booked

                if ($booking->getArticle()->getId() == $membershipArticle[$organization->getId()]) {
                    $membershipBooked = true;
                    break;
                }
            }
        }

        return $this->value === ($person->isMember($academicYear) || $membershipBooked);
    }
}
