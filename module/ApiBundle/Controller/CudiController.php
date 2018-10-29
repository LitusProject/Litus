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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ApiBundle\Controller;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Sale\Booking;
use CudiBundle\Entity\Sale\QueueItem;
use DateInterval;
use Zend\View\Model\ViewModel;

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

        if (!($person = $this->getAcademicEntity())) {
            return $this->error(401, 'The person was not found');
        }

        list($articles, $subjects) = $this->getArticlesAndSubjects($person);

        return new ViewModel(
            array(
                'result' => (object) array(
                    array(
                        'subjects' => array_values($subjects),
                        'articles' => array_values($articles),
                    ),
                ),
            )
        );
    }

    public function bookAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        if (!($person = $this->getAcademicEntity())) {
            return $this->error(401, 'The person was not found');
        }

        if (!($article = $this->getArticleEntity())) {
            return $this->error(404, 'The article was not found');
        }

        $enableBookings = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_bookings');

        $bookingsClosedExceptions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.bookings_closed_exceptions')
        );

        if (!$article->isBookable() || !($enableBookings || in_array($article->getId(), $bookingsClosedExceptions))) {
            return $this->error(500, 'The article is not bookable');
        }

        $booking = new Booking(
            $this->getEntityManager(),
            $person,
            $article,
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
            $available = $article->getStockValue() - $currentPeriod->getNbAssigned($article);
            if ($available > 0) {
                if ($available >= $booking->getNumber()) {
                    $booking->setStatus('assigned', $this->getEntityManager());
                } else {
                    $new = new Booking(
                        $this->getEntityManager(),
                        $person,
                        $article,
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

        if (!($person = $this->getAcademicEntity())) {
            return $this->error(401, 'The person was not found');
        }

        $bookingsList = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($person);

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
            ->findAllSoldByPerson($person);

        $sold = array();
        foreach ($bookingsSold as $booking) {
            $sold[] = $booking->getArticle()->getId();
        }

        list($articleList, ) = $this->getArticlesAndSubjects($person);

        $bookings = array();
        $articles = array();
        foreach ($bookingsList as $booking) {
            $bookings[] = array(
                'id'             => $booking->getId(),
                'assigned'       => $booking->getStatus() == 'assigned',
                'expirationDate' => ($booking->getExpirationDate() !== null ? $booking->getExpirationDate()->format('c') : null),
                'number'         => $booking->getNumber(),
                'article'        => $booking->getArticle()->getId(),
            );

            if (!isset($articleList[$booking->getArticle()->getId()]) && !isset($articles[$booking->getArticle()->getId()])) {
                $articles[$booking->getArticle()->getId()] = array(
                    'id'       => $booking->getArticle()->getId(),
                    'title'    => $booking->getArticle()->getMainArticle()->getTitle(),
                    'subjects' => array(),
                    'price'    => $booking->getArticle()->getSellPrice() / 100,
                    'sold'     => in_array($booking->getArticle()->getId(), $sold),
                    'bookable' => $booking->getArticle()->isBookable()
                        && $booking->getArticle()->canBook($person, $this->getEntityManager())
                        && ($enableBookings || in_array($booking->getArticle()->getId(), $bookingsClosedExceptions)),
                    'unbookable' => $booking->getArticle()->isUnbookable(),
                );
            }
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    array(
                        'bookings' => array_values($bookings),
                        'articles' => array_values($articles),
                    ),
                ),
            )
        );
    }

    public function cancelBookingAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        if ($this->getAccessToken() === null) {
            return $this->error(401, 'The access token is not valid');
        }

        if (!($booking = $this->getBookingEntity())) {
            return $this->error(404, 'The booking was not found');
        }

        if (!($booking->getArticle()->isUnbookable())) {
            return $this->error(404, 'This article cannot be unbooked');
        }

        $booking->setStatus('canceled', $this->getEntityManager());
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
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
                'status'        => 'open',
                'numberInQueue' => $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                    ->findNbBySession($session),
            );

            if ($person = $this->getAcademicEntity()) {
                $bookings = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllAssignedByPerson($person);

                $result['canSignIn'] = $session->canSignIn($this->getEntityManager(), $person);
                $result['hasBookings'] = !empty($bookings);
            }
        } else {
            $result = array(
                'status' => 'closed',
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }

    public function openingHoursAction()
    {
        $this->initJson();

        $interval = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('api.cudi_opening_hour_interval')
        );

        $openingHours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findCommingInterval($interval);

        $result = array();
        foreach ($openingHours as $openingHour) {
            $result[] = array(
                'startDate' => $openingHour->getStart()->format('c'),
                'endDate'   => $openingHour->getEnd()->format('c'),
                'comment'   => $openingHour->getComment($this->getLanguage()),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }

    public function signInAction()
    {
        $this->initJson();

        if (!($person = $this->getAcademicEntity())) {
            return $this->error(401, 'The person was not found');
        }

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOpen();

        if (sizeof($sessions) == 0) {
            return $this->error(404, 'The is no open sale session');
        }

        $session = $sessions[0];

        if (!$session->canSignIn($this->getEntityManager(), $person)) {
            return $this->error(401, 'You cannot sign in');
        }

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllAssignedByPerson($person);

        if (empty($bookings)) {
            return $this->error(401, 'You cannot sign in');
        }

        $queueItem = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneByPersonNotSold($session, $person);

        if ($queueItem === null) {
            $queueItem = new QueueItem($this->getEntityManager(), $person, $session);

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

    public function signInStatusAction()
    {
        $this->initJson();

        if (!($person = $this->getAcademicEntity())) {
            return $this->error(401, 'The person was not found');
        }

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOpen();

        if (sizeof($sessions) == 0) {
            return $this->error(404, 'The is no open sale session');
        }

        $session = $sessions[0];

        $queueItem = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneByPersonNotSold($session, $person);

        if ($queueItem === null) {
            return new ViewModel(
                array(
                    'result' => (object) array(
                        'status' => 'not_signed_in',
                    ),
                )
            );
        } else {
            return new ViewModel(
                array(
                    'result' => (object) array(
                        'status'  => $queueItem->getStatus(),
                        'number'  => $queueItem->getQueueNumber(),
                        'paydesk' => $queueItem->getPayDesk(),
                    ),
                )
            );
        }
    }

    /**
     * @param  Person $person The authenticated person
     * @return array
     */
    private function getArticlesAndSubjects(Person $person)
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
            ->findAllByAcademicAndAcademicYear($person, $currentYear);

        $bookingsSold = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllSoldByPerson($person);

        $sold = array();
        foreach ($bookingsSold as $booking) {
            $sold[] = $booking->getArticle()->getId();
        }

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
                            'id'    => $subject->getId(),
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
                            'id'       => $article->getId(),
                            'title'    => $article->getMainArticle()->getTitle(),
                            'subjects' => array(
                                array(
                                    'id'        => $subject->getId(),
                                    'mandatory' => $subjectMap->isMandatory(),
                                ),
                            ),
                            'price'    => $article->getSellPrice() / 100,
                            'sold'     => in_array($article->getId(), $sold),
                            'bookable' => $article->isBookable()
                                && $article->canBook($person, $this->getEntityManager())
                                && ($enableBookings || in_array($article->getId(), $bookingsClosedExceptions)),
                            'unbookable' => $article->isUnbookable(),
                        );
                    }
                }
            }
        }

        $subjects[0] = array(
            'id'    => 0,
            'title' => $this->getTranslator()->translate('Common'),
        );

        $commonArticles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTypeAndAcademicYear('common', $currentYear);

        foreach ($commonArticles as $commonArticle) {
            if ($commonArticle->isBookable()) {
                $articles[$commonArticle->getId()] = array(
                    'id'       => $commonArticle->getId(),
                    'title'    => $commonArticle->getMainArticle()->getTitle(),
                    'subjects' => array(
                        array(
                            'id'        => 0,
                            'mandatory' => false,
                        ),
                    ),
                    'price'    => $commonArticle->getSellPrice() / 100,
                    'sold'     => in_array($commonArticle->getId(), $sold),
                    'bookable' => $commonArticle->isBookable()
                        && $commonArticle->canBook($person, $this->getEntityManager())
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
    private function getBookingEntity()
    {
        if ($this->getRequest()->getPost('id') === null) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findOneById($this->getRequest()->getPost('id'));
    }

    /**
     * @return Article|null
     */
    private function getArticleEntity()
    {
        if ($this->getRequest()->getPost('id') === null) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getRequest()->getPost('id'));
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if ($this->getAccessToken() === null) {
            return null;
        }

        if (!($person = $this->getAccessToken()->getPerson($this->getEntityManager()))) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($person->getId());
    }
}
