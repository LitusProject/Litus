<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Controller;

use CommonBundle\Entity\Users\People\Academic,
    CudiBundle\Entity\Sales\Booking,
    CudiBundle\Form\Booking\Booking as BookingForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
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
        $authenticatedPerson = $this->getAuthentication()->getPersonObject();

        if (null === $authenticatedPerson) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($authenticatedPerson);

        $total = 0;
        foreach ($bookings as $booking) {
            $total += $booking->getArticle()->getSellPrice();
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

        if (!($booking = $this->_getBooking())) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        if (!($booking->getArticle()->isUnbookable())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The given booking cannot be cancelled!'
                )
            );

            $this->redirect()->toRoute(
                'booking',
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
        $form = new BookingForm($this->getEntityManager());

        $authenticatedPerson = $this->getAuthentication()->getPersonObject();

        if (null === $authenticatedPerson || !($authenticatedPerson instanceof Academic)) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        $currentYear = $this->getCurrentAcademicYear();

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($authenticatedPerson, $currentYear);

        $bookingsOpen = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($authenticatedPerson);

        $booked = array();
        foreach($bookingsOpen as $booking) {
            if (!isset($booked[$booking->getArticle()->getId()]))
                $booked[$booking->getArticle()->getId()] = 0;
            $booked[$booking->getArticle()->getId()] += $booking->getNumber();
        }

        $bookingsSold = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllSoldByPerson($authenticatedPerson);

        $sold = array();
        foreach($bookingsSold as $booking) {
            if (!isset($sold[$booking->getArticle()->getId()]))
                $sold[$booking->getArticle()->getId()] = 0;
            $sold[$booking->getArticle()->getId()] += $booking->getNumber();
        }

        $result = array();
        foreach ($enrollments as $enrollment) {
            $subject = $enrollment->getSubject();

            $subjectMaps = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
                ->findAllBySubjectAndAcademicYear($subject, $currentYear);

            $articles = array();
            foreach ($subjectMaps as $subjectMap) {
                $article = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Article')
                    ->findOneByArticleAndAcademicYear($subjectMap->getArticle(), $currentYear);

                if ($article !== null) {
                    $comments = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Comments\Comment')
                        ->findAllSiteByArticle($article->getMainArticle());

                    $articles[] = array(
                        'article' => $article,
                        'comments' => $comments,
                        'mandatory' => $subjectMap->isMandatory(),
                        'booked' => isset($booked[$article->getId()]) ? $booked[$article->getId()] : 0,
                        'sold' => isset($sold[$article->getId()]) ? $sold[$article->getId()] : 0,
                    );
                }
            }

            $result[] = array(
                'subject' => $subject,
                'articles' => $articles,
                'isMapping' => false
            );

            $form->addInputsForArticles($articles);
        }

        $commonArticles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findAllByTypeAndAcademicYear('common', $currentYear);

        $articles = array();
        foreach ($commonArticles as $commonArticle) {
            if ($commonArticle->isBookable()) {
                $comments = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Comments\Comment')
                    ->findAllSiteByArticle($commonArticle->getMainArticle());

                $articles[] = array(
                    'article' => $commonArticle,
                    'comments' => $comments,
                    'mandatory' => false,
                    'booked' => isset($booked[$commonArticle->getId()]) ? $booked[$commonArticle->getId()] : 0,
                    'sold' => isset($sold[$commonArticle->getId()]) ? $sold[$commonArticle->getId()] : 0,
                );
            }
        }

        $result[] = array(
            'subject' => null,
            'articles' => $articles,
            'isMapping' => false
        );

        $form->addInputsForArticles($articles);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                foreach ($formData as $formKey => $formValue) {
                    if (substr($formKey, 0, 8) === 'article-' && $formValue !== '' && $formValue !== '0') {
                        $saleArticleId = substr($formKey, 8, strlen($formKey));

                        $saleArticle = $this->getEntityManager()
                            ->getRepository('CudiBundle\Entity\Sales\Article')
                            ->findOneById($saleArticleId);

                        $booking = new Booking(
                            $this->getEntityManager(),
                            $authenticatedPerson,
                            $saleArticle,
                            'booked',
                            $formValue
                        );

                        $this->getEntityManager()->persist($booking);

                        $enableAssignment = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('cudi.enable_automatic_assignment');

                        if ($enableAssignment == '1') {
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

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCES',
                        'The textbooks have been booked!'
                    )
                );

                $this->redirect()->toRoute(
                    'booking',
                    array(
                        'action' => 'view',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'subjectArticleMap' => $result,
                'form' => $form
            )
        );
    }

    private function _getBooking()
    {
        if (null === $this->getParam('id'))
            return;

        $booking = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findOneById($this->getParam('id'));

        if (null === $booking)
            return;

        return $booking;
    }
}