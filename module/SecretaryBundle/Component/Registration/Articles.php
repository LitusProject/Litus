<?php

namespace SecretaryBundle\Component\Registration;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization;
use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\Sale\Booking;
use Doctrine\ORM\EntityManager;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Articles
{
    /**
     * @param EntityManager $entityManager
     * @param Academic      $academic
     * @param Organization  $organization
     * @param AcademicYear  $academicYear
     * @param array         $options
     */
    public static function book(EntityManager $entityManager, Academic $academic, Organization $organization, AcademicYear $academicYear, $options = array())
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

            if ($membershipArticle !== null) {
                $booking = $entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                        $membershipArticle,
                        $academic,
                        $academicYear
                    );

                if ($booking === null) {
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

                if (isset($options['payed']) && $options['payed']) {
                    $booking->setStatus('sold', $entityManager);
                }
            }
        }

        $tshirts = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.tshirt_article')
        );

        $hasTshirt = false;
        foreach ($tshirts as $tshirt) {
            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                    $entityManager
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($tshirt),
                    $academic,
                    $academicYear
                );

            if ($booking !== null) {
                $hasTshirt = true;
                break;
            }
        }

        $enableAssignment = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_automatic_assignment');
        $currentPeriod = $entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        $currentPeriod->setEntityManager($entityManager);

        if (count($tshirts) > 0 && !$hasTshirt && isset($options['tshirtSize'])) {
            $tshirtArticle = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($tshirts[$options['tshirtSize']]);

            $booking = new Booking(
                $entityManager,
                $academic,
                $tshirtArticle,
                'booked',
                1,
                true
            );

            $entityManager->persist($booking);

            if ($enableAssignment) {
                $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
                if ($available > 0) {
                    if ($available >= $booking->getNumber()) {
                        $booking->setStatus('assigned', $entityManager);
                    }
                }
            }
        }

        $dissableAssignment = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.dissable_registration_articles_2nd_stock_period');

        if (!$dissableAssignment) {
            $registrationArticles = unserialize(
                $entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.registration_articles')
            );

            foreach ($registrationArticles as $registrationArticle) {
                $booking = $entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                        $entityManager
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($registrationArticle),
                        $academic,
                        $academicYear
                    );

                if ($booking !== null) {
                    continue;
                }

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

                if ($enableAssignment) {
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

    /**
     * @param \Doctrine\ORM\EntityManager               $entityManager
     * @param \CommonBundle\Entity\User\Person\Academic $academic
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     */
    public static function cancel(EntityManager $entityManager, Academic $academic, AcademicYear $academicYear)
    {
        $ids = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        foreach ($ids as $id) {
            $membershipArticle = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);

            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneBookedOrAssignedByArticleAndPersonInAcademicYear(
                    $membershipArticle,
                    $academic,
                    $academicYear
                );

            if ($booking !== null) {
                $booking->setStatus('canceled', $entityManager);
            }
        }

        $tshirts = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.tshirt_article')
        );

        foreach ($tshirts as $tshirt) {
            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneBookedOrAssignedByArticleAndPersonInAcademicYear(
                    $entityManager
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($tshirt),
                    $academic,
                    $academicYear
                );

            if ($booking !== null) {
                $booking->setStatus('canceled', $entityManager);
            }
        }

        $registrationArticles = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.registration_articles')
        );

        foreach ($registrationArticles as $registrationArticle) {
            $booking = $entityManager
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findOneBookedOrAssignedByArticleAndPersonInAcademicYear(
                    $entityManager
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($registrationArticle),
                    $academic,
                    $academicYear
                );

            if ($booking !== null) {
                $booking->setStatus('canceled', $entityManager);
            }
        }
    }
}
