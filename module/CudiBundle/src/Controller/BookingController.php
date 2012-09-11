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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
            return new ViewModel();
        }

        $bookings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($authenticatedPerson);

        return new ViewModel(
            array(
                'bookings' => $bookings,
            )
        );
    }

    public function cancelAction()
    {
        //$this->initAjax();

        if (!($booking = $this->_getBooking()))
            return new ViewModel();

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
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return new ViewModel();
        }

        $booking->setStatus('canceled');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function bookAction()
    {
        $form = new BookingForm($this->getEntityManager());

        $authenticatedPerson = $this->getAuthentication()->getPersonObject();

        if (null === $authenticatedPerson || !($authenticatedPerson instanceof Academic)) {
            return new ViewModel();
        }

        $currentYear = $this->getCurrentAcademicYear();

        $enrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\SubjectEnrollment')
            ->findAllByAcademicAndAcademicYear($authenticatedPerson, $currentYear);

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

                    $articles[] = array(
                        'article'   => $article,
                        'mandatory' => $subjectMap->isMandatory()
                    );
                }
            }

            $result[] = array(
                'subject'   => $subject,
                'articles'  => $articles,
                'isMapping' => false,
            );

            $form->addInputsForArticles($articles);
        }

        $commonArticles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findAllByTypeAndAcademicYear('common', $currentYear);

        $articles = array();
        foreach ($commonArticles as $commonArticle) {
            $articles[] = array(
                'article'   => $commonArticle,
                'mandatory' => false,
            );
        }

        $result[] = array(
            'subject'   => null,
            'articles'  => $articles,
            'isMapping' => false,
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
                        'language' => $this->getLanguage()->getAbbrev(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'subjectArticleMap' => $result,
                'form'              => $form,
            )
        );
    }

    private function _getBooking()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the booking!'
                )
            );
    
            $this->redirect()->toRoute(
                'booking',
                array(
                    'action' => 'view',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );
    
            return;
        }
    
        $booking = $this->getEntityManager()
        ->getRepository('CudiBundle\Entity\Sales\Booking')
        ->findOneById($this->getParam('id'));
    
        if (null === $booking) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No booking with the given ID was found!'
                )
            );
    
            $this->redirect()->toRoute(
                'booking',
                array(
                    'action' => 'view',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );
    
            return;
        }
    
        return $booking;
    }

}