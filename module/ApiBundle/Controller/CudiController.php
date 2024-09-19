<?php

namespace ApiBundle\Controller;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Article as General;
use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Sale\Booking;
use CudiBundle\Entity\Sale\QueueItem;
use DateInterval;
use Laminas\View\Model\ViewModel;

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

        $person = $this->getAcademicEntity();
        if ($person === null) {
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

        $person = $this->getAcademicEntity();
        if ($person === null) {
            return $this->error(401, 'The person was not found');
        }

        $article = $this->getArticleEntity();
        if ($article === null) {
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

        $person = $this->getAcademicEntity();
        if ($person === null) {
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
                    'id'         => $booking->getArticle()->getId(),
                    'title'      => $booking->getArticle()->getMainArticle()->getTitle(),
                    'subjects'   => array(),
                    'price'      => $booking->getArticle()->getSellPrice() / 100,
                    'sold'       => in_array($booking->getArticle()->getId(), $sold),
                    'bookable'   => $booking->getArticle()->isBookable()
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

        $booking = $this->getBookingEntity();
        if ($booking === null) {
            return $this->error(404, 'The booking was not found');
        }

        $booking->getArticle()->isUnbookable();
        if ($booking === null) {
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

        if (count($sessions) > 0) {
            $session = $sessions[0];
            $result = array(
                'status'        => 'open',
                'numberInQueue' => $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                    ->findNbBySession($session),
            );

            $person = $this->getAcademicEntity();
            if ($person !== null) {
                $bookings = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllAssignedByPerson($person);

                $result['canSignIn'] = $session->canSignIn($this->getEntityManager(), $person);
                $result['hasBookings'] = count($bookings) > 0;
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
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
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

        $person = $this->getAcademicEntity();
        if ($person === null) {
            return $this->error(401, 'The person was not found');
        }

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOpen();

        if (count($sessions) == 0) {
            return $this->error(404, 'The is no open sale session');
        }

        $session = $sessions[0];

        if (!$session->canSignIn($this->getEntityManager(), $person)) {
            return $this->error(401, 'You cannot sign in');
        }

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllAssignedByPerson($person);

        if (count($bookings) == 0) {
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

        $person = $this->getAcademicEntity();
        if ($person === null) {
            return $this->error(401, 'The person was not found');
        }

        $sessions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOpen();

        if (count($sessions) == 0) {
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
     * input: json:
     * {
     *      "key": "api key",
     *      "is_same": "true/false",
     *      "barcode": barcode,
     *      "black_white": "number",
     *      "colored": "number",
     *      "official": "true/false",
     *      "recto_verso": "true/false",
     *      "purchase_price": "number",
     *      "sell_price": "number"
     * })
     */
    public function isSameAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $barcode = $this->getRequest()->getPost('barcode');

        $saleArticle = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneByBarcode($barcode);

        $articleId = $saleArticle->getMainArticle();

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article')
            ->findBy(
                array(
                    'id' => $articleId,
                )
            )[0];

        $originalArticle = $article;

        if ($article == null) {
            return $this->error(404, 'This article doesn\'t exist');
        }
        if ($this->getRequest()->getPost('is_same') === 'True') {
            $article->setIsSameAsPreviousYear(true);
        } else {
            $internal = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\Internal')
                ->findBy(
                    array(
                        'id' => $articleId,
                    )
                )[0];
            $internal->setNbBlackAndWhite($this->getRequest()->getPost('black_white'));
            $internal->setNbColored($this->getRequest()->getPost('colored'));
            $internal->setIsOfficial($this->getRequest()->getPost('official'));
            $internal->setIsRectoVerso($this->getRequest()->getPost('recto_verso'));
            $article->setIsSameAsPreviousYear(false);
        }

        $this->copyArticleSubject($originalArticle);

        $newBarcode = $this->changeBarcode($barcode);
        $saleArticle->setBarcode($newBarcode);
        $saleArticle->setPurchasePrice($this->getRequest()->getPost('purchase_price'));
        $saleArticle->setSellPrice($this->getRequest()->getPost('sell_price'));
        $saleArticle->setIsBookable(true);
        $saleArticle->setIsUnbookable(true);
        $saleArticle->setIsSellable(true);
        $saleArticle->setCanExpire(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array(
                    'front_page' => '/admin/cudi/article/file/front/' . $saleArticle->getId(),
                ),
            )
        );
    }

    private function copyArticleSubject(General $article)
    {
        $month = date('m');
        if ($month < 6 or $month > 11) {
             $currentYear = $this->getCurrentAcademicYear();
             $date = $currentYear->getEndDate();

             date_sub($date, date_interval_create_from_date_string('18 months'));

             $previousYear = $this->getEntityManager()
                 ->getRepository('CommonBundle\Entity\General\AcademicYear')
                 ->findOneByDate($date);

             $currentSubjects = $this->getEntityManager()
                 ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                 ->findAllByArticleAndAcademicYearQuery($article, $previousYear)
                 ->getResult();
            foreach ($currentSubjects as $subjectMap) {
                $mapping = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                    ->findOneByArticleAndSubjectAndAcademicYear($article, $subjectMap->getSubject(), $currentYear);

                if ($mapping === null) {
                    $newMap = new General\SubjectMap($article, $subjectMap->getSubject(), $currentYear, false);
                    $this->getEntityManager()->persist($newMap);
                }
            }
        } else {
            $currentYear = $this->getCurrentAcademicYear();
            $date = $currentYear->getEndDate();
            $date2 = $currentYear->getEndDate();
            date_add($date2, date_interval_create_from_date_string('30 days'));

            date_sub($date, date_interval_create_from_date_string('9 months'));

            $previousYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByDate($date);

            $nextYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByDate($date2);

            $currentSubjects = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllByArticleAndAcademicYearQuery($article, $previousYear)
                ->getResult();
            foreach ($currentSubjects as $subjectMap) {
                $mapping = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                    ->findOneByArticleAndSubjectAndAcademicYear($article, $subjectMap->getSubject(), $nextYear);

                if ($mapping === null) {
                    $newMap = new General\SubjectMap($article, $subjectMap->getSubject(), $nextYear, false);
                    $this->getEntityManager()->persist($newMap);
                }
            }
        }
    }

    private function changeBarcode(string $barcode)
    {
        $month = date('m');
        $currentYear = $this->getCurrentAcademicYear();

        if ($month < 6 or $month > 11) {
            $nextYearCode = $currentYear->getCode(true);
        } else {
            $date = $currentYear->getEndDate();
            date_add($date, date_interval_create_from_date_string('30 days'));
            $nextYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByDate($date);
            $nextYearCode = $nextYear->getCode(true);
        }

        return substr($barcode, 0, 3) . $nextYearCode . substr($barcode, 7);
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
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
                            'id'         => $article->getId(),
                            'title'      => $article->getMainArticle()->getTitle(),
                            'subjects'   => array(
                                array(
                                    'id'        => $subject->getId(),
                                    'mandatory' => $subjectMap->isMandatory(),
                                ),
                            ),
                            'price'      => $article->getSellPrice() / 100,
                            'sold'       => in_array($article->getId(), $sold),
                            'bookable'   => $article->isBookable()
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
                    'id'         => $commonArticle->getId(),
                    'title'      => $commonArticle->getMainArticle()->getTitle(),
                    'subjects'   => array(
                        array(
                            'id'        => 0,
                            'mandatory' => false,
                        ),
                    ),
                    'price'      => $commonArticle->getSellPrice() / 100,
                    'sold'       => in_array($commonArticle->getId(), $sold),
                    'bookable'   => $commonArticle->isBookable()
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
     * @return \CommonBundle\Entity\User\Person\Academic|null
     */
    private function getAcademicEntity()
    {
        if ($this->getAccessToken() === null) {
            return null;
        }

        $person = $this->getAccessToken()->getPerson();
        if ($person === null) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($person->getId());
    }
}
