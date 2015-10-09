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

namespace CudiBundle\Controller;

use CommonBundle\Entity\User\Person\Academic,
    CudiBundle\Entity\Article\Notification\Subscription,
    CudiBundle\Entity\Sale\Booking,
    Zend\View\Model\ViewModel;

/**
 * BookingController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class BookingController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        if (!($academic = $this->getAcademicEntity())) {
            return $this->notFoundAction();
        }

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($academic);

        $total = 0;
        foreach ($bookings as $booking) {
            $total += $booking->getArticle()->getSellPrice() * $booking->getNumber();
        }

        return new ViewModel(
            array(
                'bookings' => $bookings,
                'total' => $total,
            )
        );
    }

    public function cancelAction()
    {
        $this->initAjax();

        if (!($booking = $this->getBookingEntity())) {
            return $this->notFoundAction();
        }

        if (!($booking->getArticle()->isUnbookable())) {
            $this->flashMessenger()->error(
                'Error',
                'The given booking cannot be cancelled!'
            );

            $this->redirect()->toRoute(
                'cudi_booking',
                array(
                    'action' => 'view',
                )
            );

            return new ViewModel();
        }

        $booking->setStatus('canceled', $this->getEntityManager());
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function bookAction()
    {
        $enableBookings = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_bookings');

        $bookingsClosedExceptions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.bookings_closed_exceptions')
        );

        if (!($academic = $this->getAcademicEntity())) {
            return $this->notFoundAction();
        }

        $currentYear = $this->getCurrentAcademicYear();

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $currentYear);

        $bookingsOpen = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($academic);

        $booked = array();
        foreach ($bookingsOpen as $booking) {
            if (!isset($booked[$booking->getArticle()->getId()])) {
                $booked[$booking->getArticle()->getId()] = 0;
            }
            $booked[$booking->getArticle()->getId()] += $booking->getNumber();
        }

        $bookingsSold = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllSoldByPerson($academic);

        $sold = array();
        foreach ($bookingsSold as $booking) {
            if (!isset($sold[$booking->getArticle()->getId()])) {
                $sold[$booking->getArticle()->getId()] = 0;
            }
            $sold[$booking->getArticle()->getId()] += $booking->getNumber();
        }

        $allArticles = array();

        $result = array();
        foreach ($enrollments as $enrollment) {
            $subject = $enrollment->getSubject();

            $subjectMaps = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllBySubjectAndAcademicYear($subject, $currentYear);

            $articles = array();
            foreach ($subjectMaps as $subjectMap) {
                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneByArticle($subjectMap->getArticle());

                if ($article !== null) {
                    $comments = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Comment\Comment')
                        ->findAllSiteByArticle($article->getMainArticle());

                    $articleInfo = array(
                        'article' => $article,
                        'comments' => $comments,
                        'mandatory' => $subjectMap->isMandatory(),
                        'booked' => isset($booked[$article->getId()]) ? $booked[$article->getId()] : 0,
                        'sold' => isset($sold[$article->getId()]) ? $sold[$article->getId()] : 0,
                        'bookable' => $article->isBookable()
                            && $article->canBook($academic, $this->getEntityManager())
                            && ($enableBookings || in_array($article->getId(), $bookingsClosedExceptions)),
                    );

                    $articles[] = $articleInfo;
                    $allArticles[] = $articleInfo;
                }
            }

            $result[] = array(
                'subject' => $subject,
                'articles' => $articles,
                'isMapping' => false,
            );
        }

        $commonArticles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTypeAndAcademicYear('common', $currentYear);

        $articles = array();
        foreach ($commonArticles as $commonArticle) {
            if ($commonArticle->isBookable()) {
                $comments = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Comment\Comment')
                    ->findAllSiteByArticle($commonArticle->getMainArticle());

                $articleInfo = array(
                    'article' => $commonArticle,
                    'comments' => $comments,
                    'mandatory' => false,
                    'booked' => isset($booked[$commonArticle->getId()]) ? $booked[$commonArticle->getId()] : 0,
                    'sold' => isset($sold[$commonArticle->getId()]) ? $sold[$commonArticle->getId()] : 0,
                    'bookable' => $commonArticle->isBookable()
                        && $commonArticle->canBook($academic, $this->getEntityManager())
                        && ($enableBookings || in_array($commonArticle->getId(), $bookingsClosedExceptions)),
                );

                $articles[] = $articleInfo;
                $allArticles[] = $articleInfo;
            }
        }

        $result[] = array(
            'subject' => null,
            'articles' => $articles,
            'isMapping' => false,
        );

        $form = $this->getForm('cudi_booking_booking', array(
            'articles' => $allArticles,
        ));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $enableAssignment = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.enable_automatic_assignment');

                $total = 0;
                foreach ($formData as $formKey => $formValue) {
                    $saleArticleId = substr($formKey, 8, strlen($formKey));

                    if (!$enableBookings && !in_array($saleArticleId, $bookingsClosedExceptions)) {
                        continue;
                    }

                    if ('article-' == substr($formKey, 0, 8) && '' != $formValue && '0' != $formValue) {
                        $total += $formValue;

                        $saleArticle = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sale\Article')
                            ->findOneById($saleArticleId);

                        if (!$saleArticle->canBook($academic, $this->getEntityManager())) {
                            continue;
                        }

                        foreach ($saleArticle->getRestrictions() as $restriction) {
                            if ($restriction->getType() == 'amount') {
                                $amount = sizeof(
                                    $this->getEntityManager()
                                        ->getRepository('CudiBundle\Entity\Sale\Booking')
                                        ->findOneSoldOrAssignedOrBookedByArticleAndPersonInAcademicYear(
                                            $saleArticle,
                                            $academic,
                                            $this->getCurrentAcademicYear()
                                    )
                                );
                                if ($amount + $formValue > $restriction->getValue()) {
                                    $formValue = $restriction->getValue() - $amount;
                                }
                            }
                        }

                        $booking = new Booking(
                            $this->getEntityManager(),
                            $academic,
                            $saleArticle,
                            'booked',
                            $formValue
                        );

                        $this->getEntityManager()->persist($booking);

                        if ($enableAssignment) {
                            $currentPeriod = $this->getEntityManager()
                                ->getRepository('CudiBundle\Entity\Stock\Period')
                                ->findOneActive();
                            $currentPeriod->setEntityManager($this->getEntityManager());

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
                    }
                }

                $this->getEntityManager()->flush();

                if (0 == $total) {
                    $this->flashMessenger()->warn(
                        'Warning',
                        'You have not booked any textbooks!'
                    );
                } else {
                    $this->flashMessenger()->success(
                        'Success',
                        $enableAssignment ? 'The textbooks have been booked!' : 'The textbooks have been booked! Please wait for them to be assigned before coming to cudi.'
                    );
                }

                $this->redirect()->toRoute(
                    'cudi_booking',
                    array(
                        'action' => 'view',
                    )
                );

                return new ViewModel();
            }
        }

        $searchForm = $this->getForm('cudi_booking_search');

        return new ViewModel(
            array(
                'subjectArticleMap' => $result,
                'form' => $form,
                'searchForm' => $searchForm,
                'isSubscribed' => $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Article\Notification\Subscription')
                    ->findOneByPerson($academic) !== null,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        if (!($academic = $this->getAcademicEntity())) {
            return $this->notFoundAction();
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTitleOrAuthorAndAcademicYearQuery($this->getParam('id'), $this->getCurrentAcademicYear())
            ->setMaxResults($numResults)
            ->getResult();

        $enableBookings = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_bookings');

        $bookingsClosedExceptions = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.bookings_closed_exceptions')
        );

        $bookingsOpen = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($academic);

        $booked = array();
        foreach ($bookingsOpen as $booking) {
            if (!isset($booked[$booking->getArticle()->getId()])) {
                $booked[$booking->getArticle()->getId()] = 0;
            }
            $booked[$booking->getArticle()->getId()] += $booking->getNumber();
        }

        $bookingsSold = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllSoldByPerson($academic);

        $sold = array();
        foreach ($bookingsSold as $booking) {
            if (!isset($sold[$booking->getArticle()->getId()])) {
                $sold[$booking->getArticle()->getId()] = 0;
            }
            $sold[$booking->getArticle()->getId()] += $booking->getNumber();
        }

        $result = array();
        foreach ($articles as $article) {
            if (!$article->isBookable() && $article->getMainArticle()->getType() == 'common') {
                continue;
            }

            $item = (object) array();
            $item->id = $article->getId();
            $item->title = $article->getMainArticle()->getTitle();
            $item->authors = $article->getMainArticle()->getAuthors();
            $item->price = number_format($article->getSellPrice() / 100, 2);
            $item->discounts = array();

            foreach ($article->getDiscounts() as $discount) {
                $item->discounts[] = array(
                    'type' => $this->getTranslator()->translate($discount->getType()),
                    'price' => number_format($discount->apply($article->getSellPrice()) / 100, 2),
                );
            }

            $item->bookable = $article->isBookable()
                && $article->canBook($academic, $this->getEntityManager())
                && ($enableBookings || in_array($article->getId(), $bookingsClosedExceptions));
            $item->booked = isset($booked[$article->getId()]) ? $booked[$article->getId()] : 0;
            $item->sold = isset($sold[$article->getId()]) ? $sold[$article->getId()] : 0;
            $item->comments = array();

            $comments = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Comment\Comment')
                ->findAllSiteByArticle($article->getMainArticle());

            foreach ($comments as $comment) {
                $item->comments[] = $comment->getText();
            }

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function bookSearchAction()
    {
        $this->initAjax();

        if (!($academic = $this->getAcademicEntity())) {
            return $this->notFoundAction();
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            $enableBookings = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.enable_bookings');

            $bookingsClosedExceptions = unserialize(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.bookings_closed_exceptions')
            );

            $enableAssignment = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.enable_automatic_assignment');

            $currentPeriod = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findOneActive();
            $currentPeriod->setEntityManager($this->getEntityManager());

            foreach ($formData as $id => $number) {
                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($id);

                if (null === $article || !is_numeric($number)) {
                    continue;
                }

                if ($article->isBookable() && ($enableBookings || in_array($article->getId(), $bookingsClosedExceptions))) {
                    $booking = new Booking(
                        $this->getEntityManager(),
                        $academic,
                        $article,
                        'booked',
                        $number
                    );

                    $this->getEntityManager()->persist($booking);

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
                }
            }

            $this->getEntityManager()->flush();

            $this->flashMessenger()->success(
                'Success',
                'The textbooks have been booked!'
            );

            return new ViewModel(
                array(
                    'result' => array('status' => 'success'),
                )
            );
        }

        $this->flashMessenger()->error(
            'Error',
            'The textbooks couldn\'t be booked!'
        );

        return new ViewModel(
            array(
                'result' => array('status' => 'error'),
            )
        );
    }

    public function keepUpdatedAction()
    {
        $this->initAjax();

        if (!($academic = $this->getAcademicEntity())) {
            return $this->notFoundAction();
        }

        if ($this->getRequest()->getPost()['keepUpdated'] == 'true') {
            $this->getEntityManager()->persist(new Subscription($academic));
        } else {
            $subscription = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\Notification\Subscription')
                ->findOneByPerson($academic);
            if (null !== $subscription) {
                $this->getEntityManager()->remove($subscription);
            }
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array('status' => 'success'),
            )
        );
    }

    /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            return;
        }

        return $academic;
    }

    /**
     * @return Booking
     */
    private function getBookingEntity()
    {
        if (null === $this->getParam('id') || !is_numeric($this->getParam('id'))) {
            return;
        }

        if (!($academic = $this->getAcademicEntity())) {
            return;
        }

        $booking = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findOneById($this->getParam('id'));

        if ($booking->getPerson() !== $academic) {
            return;
        }

        return $booking;
    }
}
