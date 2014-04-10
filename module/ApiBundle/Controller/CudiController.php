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

namespace ApiBundle\Controller;

use DateInterval,
    DateTime,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * CudiController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */

class CudiController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function articlesAction()
    {
        $this->initJson();

        if (null === $this->getAccessToken())
            return $this->error(401, 'The access token is not valid');

        $enableBookings = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_bookings');

        $bookingsClosedExceptions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.bookings_closed_exceptions')
        );

        $authenticatedPerson = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->_getPerson()->getId());

        if (null === $authenticatedPerson)
            return $this->error(500, 'The person is not an academic');

        $currentYear = $this->getCurrentAcademicYear();

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($authenticatedPerson, $currentYear);

        $bookingsSold = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllSoldByPerson($authenticatedPerson);

        $sold = array();
        foreach ($bookingsSold as $booking)
            $sold[] = $booking->getArticle()->getId();

        $result = array();
        foreach ($enrollments as $enrollment) {
            $subject = $enrollment->getSubject();

            $subjectMaps = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllBySubjectAndAcademicYear($subject, $currentYear);

            foreach ($subjectMaps as $subjectMap) {
                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneByArticle($subjectMap->getArticle());

                if ($article !== null) {
                    $result[] = array(
                        'id'             => $article->getId(),
                        'title'          => $article->getMainArticle()->getTitle(),
                        'subject'        => $subject->getName(),
                        'price'          => $article->getSellPrice()/100,
                        'mandatory'      => $subjectMap->isMandatory(),
                        'sold'           => in_array($article->getId(), $sold),
                        'bookable'       => $article->isBookable()
                            && $article->canBook($authenticatedPerson, $this->getEntityManager())
                            && ($enableBookings || in_array($article->getId(), $bookingsClosedExceptions)),
                    );
                }
            }
        }

        $commonArticles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTypeAndAcademicYear('common', $currentYear);

        $articles = array();
        foreach ($commonArticles as $commonArticle) {
            if ($commonArticle->isBookable()) {
                $result[] = array(
                    'id'        => $commonArticle->getId(),
                    'title'     => $commonArticle->getMainArticle()->getTitle(),
                    'subject'   => 'Common',
                    'price'     => $commonArticle->getSellPrice()/100,
                    'mandatory' => false,
                    'sold'      => isset($sold[$commonArticle->getId()]) ? $sold[$commonArticle->getId()] : 0,
                    'bookable'  => $commonArticle->isBookable()
                        && $commonArticle->canBook($authenticatedPerson, $this->getEntityManager())
                        && ($enableBookings || in_array($commonArticle->getId(), $bookingsClosedExceptions)),
                );
            }
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    public function bookingsAction()
    {
        $this->initJson();

        if (null === $this->getAccessToken())
            return $this->error(401, 'The access token is not valid');

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($this->_getPerson());

        $result = array();
        foreach ($bookings as $booking) {
            $result[] = array(
                'id'             => $booking->getId(),
                'expirationDate' => (null !== $booking->getExpirationDate() ? $booking->getExpirationDate()->format('c') : null),
                'number'         => $booking->getNumber(),
                'article'        => $booking->getArticle()->getId(),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    public function cancelBookingAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost())
            return $this->error(405, 'This endpoint can only be accessed through POST');

        if (null === $this->getAccessToken())
            return $this->error(401, 'The access token is not valid');

        if (null === $this->_getBooking())
            return $this->error(500, 'The booking was not found');

        if (!($this->_getBooking()->getArticle()->isUnbookable()))
            return $this->error(500, 'This article cannot be unbooked');

        $this->_getBooking()->setStatus('canceled', $this->getEntityManager());
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array()
            )
        );
    }

    public function currentSessionAction()
    {
        $this->initJson();

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOpen();

        if (sizeof($sessions) >= 1) {
            $result = array(
                'status' => 'open',
                'numberInQueue' => $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                    ->findNbBySession($sessions[0]),
            );
        } else {
            $result = array(
                'status' => 'closed',
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    public function openingHoursAction()
    {
        $this->initJson();

        $openingHours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findCurrentWeek();

        $result = array();
        foreach ($openingHours as $openingHour) {
            $result[] = array(
                'startDate' => $openingHour->getStart()->format('c'),
                'endDate' => $openingHour->getEnd()->format('c'),
                'comment' => $openingHour->getComment($this->getLanguage()),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }

    private function _getBooking()
    {
        if (null === $this->getRequest()->getPost('id'))
            return null;

        return $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findOneById($this->getRequest()->getPost('id'));
    }

    private function _getPerson()
    {
        if (null === $this->getAccessToken())
            return null;

        return $this->getAccessToken()->getPerson($this->getEntityManager());
    }
}
