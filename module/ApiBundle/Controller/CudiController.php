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

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Sale\Booking,
    CudiBundle\Entity\Sale\QueueItem,
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

        $authenticatedPerson = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->_getPerson()->getId());

        if (null === $authenticatedPerson)
            return $this->error(500, 'The person is not an academic');

        list($articles, $subjects) = $this->_getArticlesAndSubjects($authenticatedPerson);

        return new ViewModel(
            array(
                'result' => (object) array(
                    array(
                        'subjects' => array_values($subjects),
                        'articles' => array_values($articles),
                    )
                )
            )
        );
    }

    public function bookAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost())
            return $this->error(405, 'This endpoint can only be accessed through POST');

        if (null === $this->getAccessToken())
            return $this->error(401, 'The access token is not valid');

        if (null === $this->_getArticle())
            return $this->error(500, 'The article was not found');

        $authenticatedPerson = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->_getPerson()->getId());

        if (null === $authenticatedPerson)
            return $this->error(500, 'The person is not an academic');

        $enableBookings = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_bookings');

        $bookingsClosedExceptions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.bookings_closed_exceptions')
        );

        if (!$this->_getArticle()->isBookable() || !($enableBookings || in_array($this->_getArticle()->getId(), $bookingsClosedExceptions)))
            return $this->error(500, 'The article is not bookable');

        $booking = new Booking(
            $this->getEntityManager(),
            $this->_getPerson(),
            $this->_getArticle(),
            'booked',
            1
        );

        $this->getEntityManager()->persist($booking);

        $enableAssignment = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_automatic_assignment');

        $currentPeriod = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        $currentPeriod->setEntityManager($this->getEntityManager());

        if ($enableAssignment) {
            $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
            if ($available > 0) {
                if ($available >= $booking->getNumber()) {
                    $booking->setStatus('assigned', $this->getEntityManager());
                } else {
                    $new = new Booking(
                        $this->getEntityManager(),
                        $booking->getPerson(),
                        $booking->getArticle(),
                        'booked',
                        $booking->getNumber() - $available
                    );

                    $this->getEntityManager()->persist($new);
                    $booking->setNumber($available)
                        ->setStatus('assigned', $this->getEntityManager());
                }
            }
        }

        $this->getEntityManager()->flush();

        return $this->bookingsAction();
    }

    public function bookingsAction()
    {
        $this->initJson();

        if (null === $this->getAccessToken())
            return $this->error(401, 'The access token is not valid');

        $authenticatedPerson = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->_getPerson()->getId());

        if (null === $authenticatedPerson)
            return $this->error(500, 'The person is not an academic');

        $bookingsList = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($this->_getPerson());

        $enableBookings = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_bookings');

        $bookingsClosedExceptions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.bookings_closed_exceptions')
        );

        $bookingsSold = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllSoldByPerson($authenticatedPerson);

        $sold = array();
        foreach ($bookingsSold as $booking)
            $sold[] = $booking->getArticle()->getId();

        list($articleList, ) = $this->_getArticlesAndSubjects($authenticatedPerson);

        $bookings = array();
        $articles = array();
        foreach ($bookingsList as $booking) {
            $bookings[] = array(
                'id'             => $booking->getId(),
                'assigned'       => $booking->getStatus() == 'assigned',
                'expirationDate' => (null !== $booking->getExpirationDate() ? $booking->getExpirationDate()->format('c') : null),
                'number'         => $booking->getNumber(),
                'article'        => $booking->getArticle()->getId(),
            );

            if (!isset($articleList[$booking->getArticle()->getId()]) && !isset($articles[$booking->getArticle()->getId()])) {
                $articles[$booking->getArticle()->getId()] = array(
                    'id'             => $booking->getArticle()->getId(),
                    'title'          => $booking->getArticle()->getMainArticle()->getTitle(),
                    'subjects'       => array(),
                    'price'          => $booking->getArticle()->getSellPrice()/100,
                    'sold'           => in_array($booking->getArticle()->getId(), $sold),
                    'bookable'       => $booking->getArticle()->isBookable()
                        && $booking->getArticle()->canBook($authenticatedPerson, $this->getEntityManager())
                        && ($enableBookings || in_array($booking->getArticle()->getId(), $bookingsClosedExceptions)),
                    'unbookable'       => $booking->getArticle()->isUnbookable(),
                );
            }
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    array(
                        'bookings' => array_values($bookings),
                        'articles' => array_values($articles),
                    )
                )
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
            $session = $sessions[0];
            $result = array(
                'status' => 'open',
                'numberInQueue' => $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                    ->findNbBySession($session),
            );

            if ($person = $this->_getPerson()) {
                $bookings = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllAssignedByPerson($this->_getPerson());

                $result['canSignIn'] = $session->canSignIn($this->getEntityManager(), $this->_getPerson());
                $result['hasBookings'] = !empty($bookings);
            }
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

    public function signInAction()
    {
        $this->initJson();

        if (null === $this->getAccessToken())
            return $this->error(401, 'The access token is not valid');

        $authenticatedPerson = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->_getPerson()->getId());

        if (null === $authenticatedPerson)
            return $this->error(500, 'The person is not an academic');

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOpen();

        if (sizeof($sessions) == 0)
            return $this->error(500, 'The is no open sale session');

        $session = $sessions[0];

        if (!$session->canSignIn($this->getEntityManager(), $authenticatedPerson))
            return $this->error(500, 'You cannot sign in');

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllAssignedByPerson($authenticatedPerson);

        if (empty($bookings))
            return $this->error(500, 'You cannot sign in');

        $queueItem = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneByPersonNotSold($session, $authenticatedPerson);

        if (null == $queueItem) {
            $queueItem = new QueueItem($this->getEntityManager(), $authenticatedPerson, $session);

            $this->getEntityManager()->persist($queueItem);
            $this->getEntityManager()->flush();
        } elseif ($queueItem->getStatus() == 'hold') {
            $queueItem->setStatus('signed_in');
            $this->getEntityManager()->flush();
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'number' => $queueItem->getQueueNumber(),
                ),
            )
        );
    }

    /**
     * @param Person $authenticatedPerson
     * @return array
     */
    private function _getArticlesAndSubjects(Person $authenticatedPerson)
    {
        $enableBookings = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_bookings');

        $bookingsClosedExceptions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.bookings_closed_exceptions')
        );

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

        $articles = array();
        $subjects = array();
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
                    if (!isset($subjects[$subject->getId()])) {
                        $subjects[$subject->getId()] = array(
                            'id' => $subject->getId(),
                            'title' => $subject->getName(),
                        );
                    }

                    if (isset($articles[$article->getId()])) {
                        $articles[$article->getId()]['subjects'][] = array(
                            'id'        => $subject->getId(),
                            'mandatory' => $subjectMap->isMandatory(),
                        );
                    } else {
                        $articles[$article->getId()] = array(
                            'id'             => $article->getId(),
                            'title'          => $article->getMainArticle()->getTitle(),
                            'subjects'       => array(
                                array(
                                    'id'        => $subject->getId(),
                                    'mandatory' => $subjectMap->isMandatory(),
                                )
                            ),
                            'price'          => $article->getSellPrice()/100,
                            'sold'           => in_array($article->getId(), $sold),
                            'bookable'       => $article->isBookable()
                                && $article->canBook($authenticatedPerson, $this->getEntityManager())
                                && ($enableBookings || in_array($article->getId(), $bookingsClosedExceptions)),
                            'unbookable'       => $article->isUnbookable(),
                        );
                    }
                }
            }
        }

        $subjects[0] = array(
            'id' => 0,
            'title' => $this->getTranslator()->translate('Common'),
        );

        $commonArticles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTypeAndAcademicYear('common', $currentYear);

        foreach ($commonArticles as $commonArticle) {
            if ($commonArticle->isBookable()) {
                $articles[$commonArticle->getId()] = array(
                    'id'        => $commonArticle->getId(),
                    'title'     => $commonArticle->getMainArticle()->getTitle(),
                    'subjects'  => array(
                        array(
                            'id'        => 0,
                            'mandatory' => false,
                        )
                    ),
                    'price'     => $commonArticle->getSellPrice()/100,
                    'sold'      => isset($sold[$commonArticle->getId()]) ? $sold[$commonArticle->getId()] : 0,
                    'bookable'  => $commonArticle->isBookable()
                        && $commonArticle->canBook($authenticatedPerson, $this->getEntityManager())
                        && ($enableBookings || in_array($commonArticle->getId(), $bookingsClosedExceptions)),
                    'unbookable' => $commonArticle->isUnbookable(),
                );
            }
        }

        return array($articles, $subjects);
    }

    /**
     * @return Booking|null
     */
    private function _getBooking()
    {
        if (null === $this->getRequest()->getPost('id'))
            return null;

        return $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findOneById($this->getRequest()->getPost('id'));
    }

    /**
     * @return Article|null
     */
    private function _getArticle()
    {
        if (null === $this->getRequest()->getPost('id'))
            return null;

        return $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getRequest()->getPost('id'));
    }

    /**
     * @return Person|null
     */
    private function _getPerson()
    {
        if (null === $this->getAccessToken())
            return null;

        return $this->getAccessToken()->getPerson($this->getEntityManager());
    }
}
