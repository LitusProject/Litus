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

namespace SecretaryBundle\Component\Registration;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    CommonBundle\Entity\User\Person\Academic,
    CudiBundle\Entity\Sale\Booking,
    Doctrine\ORM\EntityManager;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Articles
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\User\Person\Academic $academic
     * @param \CommonBundle\Entity\General\Organization $organization
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param array $options
     */
    static public function book(EntityManager $entityManager, Academic $academic, Organization $organization, AcademicYear $academicYear, $options = array())
    {
        $ids = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        if (isset($ids[$organization->getId()])) {
            $membershipArticle = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($ids[$organization->getId()]);

            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneSoldOrAssignedOrBookedByArticleAndPerson(
                    $membershipArticle,
                    $academic
                );

            if (null === $booking) {
                $booking = new Booking(
                    $entityManager,
                    $academic,
                    $membershipArticle,
                    'assigned',
                    1,
                    true
                );

                $entityManager->persist($booking);
            }

            if (isset($options['payed']) && $options['payed'])
                $booking->setStatus('sold', $entityManager);
        }

        $enableAssignment = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_automatic_assignment');
        $currentPeriod = $entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        $currentPeriod->setEntityManager($entityManager);

        $registrationArticles = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.registration_articles')
        );

        foreach ($registrationArticles as $registrationArticle) {
            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneSoldOrAssignedOrBookedByArticleAndPerson(
                    $entityManager
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($registrationArticle),
                    $academic
                );

            if (null !== $booking)
                continue;

            $booking = new Booking(
                $entityManager,
                $academic,
                $entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($registrationArticle),
                'booked',
                1,
                true
            );
            $entityManager->persist($booking);

            if ($enableAssignment == '1') {
                $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                if ($available > 0) {
                    if ($available >= $booking->getNumber()) {
                        $booking->setStatus('assigned', $entityManager);
                    }
                }
            }
        }
    }
}