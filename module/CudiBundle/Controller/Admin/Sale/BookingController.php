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

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Component\Mail\Booking as BookingMail,
    CudiBundle\Entity\Sale\Booking,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Form\Admin\Mail\Send as MailForm,
    CudiBundle\Form\Admin\Sales\Booking\Add as AddForm,
    CudiBundle\Form\Admin\Sales\Booking\Article as ArticleForm,
    CudiBundle\Form\Admin\Sales\Booking\Person as PersonForm,
    DateTime,
    DateInterval,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * BookingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BookingController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();

        if (null !== $this->getParam('field'))
            $bookings = $this->_search($activePeriod, $this->getParam('type'));

        if (!isset($bookings)) {
            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllActiveByPeriodQuery($activePeriod);
        }

        $paginator = $this->paginator()->createFromQuery(
            $bookings,
            $this->getParam('page')
        );

        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();

        return new ViewModel(
            array(
                'periods' => $periods,
                'activePeriod' => $activePeriod,
                'currentPeriod' => $currentPeriod,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function inactiveAction()
    {
        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();

        if (null !== $this->getParam('field'))
            $bookings = $this->_search($activePeriod, 'inactive');

        if (!isset($bookings)) {
            $bookings = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllInactiveByPeriodQuery($activePeriod);
        }

        $paginator = $this->paginator()->createFromQuery(
            $bookings,
            $this->getParam('page')
        );

        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();

        return new ViewModel(
            array(
                'periods' => $periods,
                'activePeriod' => $activePeriod,
                'currentPeriod' => $currentPeriod,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();

        $academicYear = $this->getAcademicYear();

        $form = new AddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

                $booking = new Booking(
                    $this->getEntityManager(),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['person_id']),
                    $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Sale\Article')
                        ->findOneById($formData['article_id']),
                    'booked',
                    $formData['amount'],
                    true
                );

                $this->getEntityManager()->persist($booking);

                $enableAssignment = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.enable_automatic_assignment');

                if ($enableAssignment) {
                    $currentPeriod = $this->getActiveStockPeriod();

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

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The booking was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_booking',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel(
                    array(
                        'currentAcademicYear' => $academicYear,
                    )
                );
            }
        }

        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();

        return new ViewModel(
            array(
                'periods' => $periods,
                'activePeriod' => $activePeriod,
                'currentPeriod' => $currentPeriod,
                'currentAcademicYear' => $academicYear,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($booking = $this->_getBooking()))
            return new ViewModel();

        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();

        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();

        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllByPersonAndPeriodQuery($booking->getPerson(), $activePeriod),
            $this->getParam('page')
        );

        $mailForm = new MailForm($booking->getPerson()->getEmail(), $booking->getPerson()->getFullName());
        $mailForm->setAttribute('action', $this->url()->fromRoute('cudi_admin_mail'));

        return new ViewModel(
            array(
                'mailForm' => $mailForm,
                'periods' => $periods,
                'activePeriod' => $activePeriod,
                'currentPeriod' => $currentPeriod,
                'booking' => $booking,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function deleteAction()
    {
        if (!($booking = $this->_getBooking()))
            return new ViewModel();

        if (is_numeric($this->getParam('number')) && $this->getParam('number') < $booking->getNumber()) {
            $booking->setNumber($booking->getNumber() - $this->getParam('number'));
        } else {
            $this->getEntityManager()->remove($booking);
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The booking was successfully removed!'
            )
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'manage'
            )
        );

        return new ViewModel();
    }

    public function assignAction()
    {
        if (!($booking = $this->_getBooking()))
            return new ViewModel();

        if (!($currentPeriod = $this->getActiveStockPeriod()))
            return new ViewModel();

        $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
        if ($available <= 0) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'The booking could not be assigned! Not enough articles in stock.'
                )
            );

            $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

            return new ViewModel();
        }

        if (is_numeric($this->getParam('number')) && $this->getParam('number') < $booking->getNumber()) {
            $new = new Booking(
                $this->getEntityManager(),
                $booking->getPerson(),
                $booking->getArticle(),
                'booked',
                $booking->getNumber() - $this->getParam('number')
            );
            $this->getEntityManager()->persist($new);
            $booking->setNumber($this->getParam('number'));
        } else {
            $number = $booking->getNumber();
        }

        if ($available < $booking->getNumber()) {
            $new = new Booking(
                $this->getEntityManager(),
                $booking->getPerson(),
                $booking->getArticle(),
                'booked',
                $booking->getNumber() - $available
            );
            $this->getEntityManager()->persist($new);
            $booking->setNumber($available);
        }

        $booking->setStatus('assigned', $this->getEntityManager());

        BookingMail::sendAssignMail($this->getEntityManager(), $this->getMailTransport(), array($booking), $booking->getPerson());

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The booking was successfully assigned!'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function unassignAction()
    {
        if (!($booking = $this->_getBooking()))
            return new ViewModel();

        if (is_numeric($this->getParam('number')) && $this->getParam('number') < $booking->getNumber()) {
            $new = new Booking(
                $this->getEntityManager(),
                $booking->getPerson(),
                $booking->getArticle(),
                'booked',
                $this->getParam('number')
            );
            $this->getEntityManager()->persist($new);
            $booking->setNumber($booking->getNumber() - $this->getParam('number'));
        } else {
            $booking->setStatus('booked', $this->getEntityManager());
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The booking was successfully unassigned!'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function expireAction()
    {
        if (!($booking = $this->_getBooking()))
            return new ViewModel();

        $booking->setStatus('expired', $this->getEntityManager());
        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The booking was successfully expired!'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function extendAction()
    {
        if (!($booking = $this->_getBooking()))
            return new ViewModel();

        $extendTime = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.reservation_extend_time');

        if ($booking->getExpirationDate()) {
            $date = clone $booking->getExpirationDate();
            $booking->setExpirationDate($date->add(new DateInterval($extendTime)));
            $this->getEntityManager()->flush();
        }

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                'The booking was successfully extended!'
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function deleteAllAction()
    {
        $excluded = explode(',', $this->getParam('string'));

        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->cancelAll($this->getAuthentication()->getPersonObject(), $this->getParam('type') == 'remove_registration', $excluded);

        if (0 == $number)
            $message = 'No booking could be removed!';
        elseif (1 == $number)
            $message = 'There is <b>one</b> booking removed!';
        else
            $message = 'There are <b>' . $number . '</b> bookings removed!';

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                $message
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function assignAllAction()
    {
        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->assignAll($this->getAuthentication()->getPersonObject(), $this->getMailTransport());

        if (0 == $number)
            $message = 'No booking could be assigned!';
        elseif (1 == $number)
            $message = 'There is <b>one</b> booking assigned!';
        else
            $message = 'There are <b>' . $number . '</b> bookings assigned!';

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                $message
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function expireAllAction()
    {
        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->expireBookings($this->getMailTransport());

        $this->getEntityManager()->flush();

        if (0 == $number)
            $message = 'No booking could be expired!';
        elseif (1 == $number)
            $message = 'There is <b>one</b> booking expired!';
        else
            $message = 'There are <b>' . $number . '</b> bookings expired!';

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                $message
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function extendAllAction()
    {
        $date = DateTime::createFromFormat('d#m#Y H:i', $this->getParam('date') . ' 0:00');
        $date->add(new DateInterval('P1D'));

        $number = 0;
        if ($date) {
            $number = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->extendAllBookings($date);

            $this->getEntityManager()->flush();
        }

        if (0 == $number)
            $message = 'No booking could be extended!';
        elseif (1 == $number)
            $message = 'There is <b>one</b> booking extended!';
        else
            $message = 'There are <b>' . $number . '</b> bookings extended!';

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'SUCCESS',
                $message
            )
        );

        $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);

        return new ViewModel();
    }

    public function searchAction()
    {
        $this->initAjax();

        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $bookings = $this->_search($activePeriod, 'active')
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach($bookings as $booking) {
            $item = (object) array();
            $item->id = $booking->getId();
            $item->person = $booking->getPerson()->getFullName();
            $item->article = $booking->getArticle()->getMainArticle()->getTitle();
            $item->number = $booking->getNumber();
            $item->bookDate = $booking->getBookDate()->format('d/m/Y H:i');
            $item->status = ucfirst($booking->getStatus());
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function personAction()
    {
        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();

        $return = new ViewModel();

        $form = new PersonForm();
        $return->form = $form;

        if ($person = $this->_getPerson()) {
            $paginator = $this->paginator()->createFromQuery(
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByPersonAndPeriodQuery($person, $activePeriod),
                $this->getParam('page')
            );

            $return->paginator = $paginator;
            $return->paginationControl = $this->paginator()->createControl();
            $return->person = $person;
        }

        return $return;
    }

    public function articleAction()
    {
        if (!($activePeriod = $this->_getPeriod()))
            return new ViewModel();

        $return = new ViewModel();

        $form = new ArticleForm();
        $return->form = $form;
        $return->currentAcademicYear = $this->getAcademicYear();;

        if ($article = $this->_getArticle()) {
            $paginator = $this->paginator()->createFromQuery(
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByArticleAndPeriodQuery($article, $activePeriod),
                $this->getParam('page')
            );

            $return->paginator = $paginator;
            $return->paginationControl = $this->paginator()->createControl(true);
            $return->article = $article;
        }

        return $return;
    }

    public function actionsAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Log')
                ->findBookingLogsQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function undoAction()
    {
        if (!($log = $this->_getLog()))
            return new ViewModel();

        if ($log->getType() == 'assigments') {
            $ids = $log->getAssigments();
            foreach($ids as $id) {
                $booking = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneById($id);
                $booking->setStatus('booked', $this->getEntityManager());
            }
        } elseif ($log->getType() == 'cancellations') {
            $ids = $log->getCancellations();
            foreach($ids as $id) {
                $booking = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneById($id);
                $booking->setStatus('booked', $this->getEntityManager());
            }
        }

        $this->getEntityManager()->remove($log);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _search(Period $activePeriod, $type)
    {
        switch($this->getParam('field')) {
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByPersonNameAndTypeAndPeriodQuery($this->getParam('string'), $type, $activePeriod);
            case 'article':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByArticleAndTypeAndPeriodQuery($this->getParam('string'), $type, $activePeriod);
            case 'status':
                return $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByStatusAndTypeAndPeriodQuery($this->getParam('string'), $type, $activePeriod);
        }
    }

    private function _getPeriod()
    {
        if (null === $this->getParam('period')) {
            return $this->getActiveStockPeriod();
        }

        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneById($this->getParam('period'));

        if (null === $period) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No period with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $period;
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
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $booking = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
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
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $booking;
    }

    private function _getPerson()
    {
        if (null === $this->getParam('id')) {
            return;
        }

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->getParam('id'));

        if (null === $person) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No person with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'person'
                )
            );

            return;
        }

        return $person;
    }

    private function _getArticle()
    {
        if (null === $this->getParam('id')) {
            return;
        }

        $article = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($this->getParam('id'));

        if (null === $article) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No article with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'article'
                )
            );

            return;
        }

        return $article;
    }

    private function _getLog()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the log!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $log = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Log')
            ->findOneById($this->getParam('id'));

        if (null === $log) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No log with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $log;
    }
}
