<?php

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Component\Mail\Booking as BookingMail;
use CudiBundle\Entity\Log;
use CudiBundle\Entity\Sale\Article as SaleArticle;
use CudiBundle\Entity\Sale\Booking;
use CudiBundle\Entity\Sale\QueueItem;
use CudiBundle\Entity\Sale\ReturnItem;
use CudiBundle\Entity\Stock\Period;
use DateInterval;
use DateTime;
use Laminas\View\Model\ViewModel;

/**
 * BookingController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BookingController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $currentPeriod = $this->getActiveStockPeriodEntity();
        if ($currentPeriod === null) {
            return new ViewModel();
        }

        $activePeriod = $this->getPeriodEntity();
        if ($activePeriod === null) {
            return new ViewModel();
        }

        if ($this->getParam('field') !== null) {
            $bookings = $this->search($activePeriod, $this->getParam('type'));
        }

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
                'periods'           => $periods,
                'activePeriod'      => $activePeriod,
                'currentPeriod'     => $currentPeriod,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function inactiveAction()
    {
        $currentPeriod = $this->getActiveStockPeriodEntity();
        if ($currentPeriod === null) {
            return new ViewModel();
        }

        $activePeriod = $this->getPeriodEntity();
        if ($activePeriod === null) {
            return new ViewModel();
        }

        if ($this->getParam('field') !== null) {
            $bookings = $this->search($activePeriod, 'inactive');
        }

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
                'periods'           => $periods,
                'activePeriod'      => $activePeriod,
                'currentPeriod'     => $currentPeriod,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $currentPeriod = $this->getActiveStockPeriodEntity();
        if ($currentPeriod === null) {
            return new ViewModel();
        }

        $activePeriod = $this->getPeriodEntity();
        if ($activePeriod === null) {
            return new ViewModel();
        }

        $academicYear = $this->getAcademicYearEntity();

        $form = $this->getForm('cudi_sale_booking_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $booking = $form->hydrateObject();

                $this->getEntityManager()->persist($booking);

                $enableAssignment = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.enable_automatic_assignment');

                if ($enableAssignment) {
                    $currentPeriod = $this->getActiveStockPeriodEntity();

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

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The booking was successfully created!'
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_booking',
                    array(
                        'action' => 'manage',
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
                'periods'             => $periods,
                'activePeriod'        => $activePeriod,
                'currentPeriod'       => $currentPeriod,
                'currentAcademicYear' => $academicYear,
                'form'                => $form,
            )
        );
    }

    public function editAction()
    {
        $booking = $this->getBookingEntity();
        if ($booking === null) {
            return new ViewModel();
        }

        $currentPeriod = $this->getActiveStockPeriodEntity();
        if ($currentPeriod === null) {
            return new ViewModel();
        }

        $activePeriod = $this->getPeriodEntity();
        if ($activePeriod === null) {
            return new ViewModel();
        }

        $periods = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findAll();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->findAllByPersonAndAcademicYearQuery($booking->getPerson(), $this->getAcademicYearEntity()),
            $this->getParam('page')
        );

        $mailForm = $this->getForm(
            'cudi_mail_send',
            array(
                'email' => $booking->getPerson()->getEmail(),
                'name'  => $booking->getPerson()->getFullName(),
            )
        );
        $mailForm->setAttribute('action', $this->url()->fromRoute('cudi_admin_mail'));

        return new ViewModel(
            array(
                'mailForm'          => $mailForm,
                'periods'           => $periods,
                'activePeriod'      => $activePeriod,
                'currentPeriod'     => $currentPeriod,
                'booking'           => $booking,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function deleteAction()
    {
        $booking = $this->getBookingEntity();
        if ($booking === null) {
            return new ViewModel();
        }

        if (is_numeric($this->getParam('number')) && $this->getParam('number') < $booking->getNumber()) {
            $booking->setNumber($booking->getNumber() - $this->getParam('number'));
        } else {
            $this->getEntityManager()->remove($booking);
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The booking was successfully removed!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function assignAction()
    {
        $booking = $this->getBookingEntity();
        if ($booking === null) {
            return new ViewModel();
        }

        $currentPeriod = $this->getActiveStockPeriodEntity();
        if ($currentPeriod === null) {
            return new ViewModel();
        }

        $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
        if ($available <= 0) {
            $this->flashMessenger()->error(
                'Error',
                'The booking could not be assigned! Not enough articles in stock.'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'edit',
                    'id'     => $booking->getId(),
                )
            );

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
        $this->getEntityManager()->flush();

        // Only send the mail if the article ID is not 9044
        if ($booking->getArticle()->getId() != 9044) {
            BookingMail::sendAssignMail($this->getEntityManager(), $this->getMailTransport(), array($booking), $booking->getPerson());
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            'The booking was successfully assigned!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'edit',
                'id'     => $booking->getId(),
            )
        );

        return new ViewModel();
    }

    public function unassignAction()
    {
        $booking = $this->getBookingEntity();
        if ($booking === null) {
            return new ViewModel();
        }

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

        $this->flashMessenger()->success(
            'SUCCESS',
            'The booking was successfully unassigned!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'edit',
                'id'     => $booking->getId(),
            )
        );

        return new ViewModel();
    }

    public function expireAction()
    {
        $booking = $this->getBookingEntity();
        if ($booking === null) {
            return new ViewModel();
        }

        $booking->setStatus('expired', $this->getEntityManager());
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The booking was successfully expired!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'edit',
                'id'     => $booking->getId(),
            )
        );

        return new ViewModel();
    }

    public function extendAction()
    {
        $booking = $this->getBookingEntity();
        if ($booking === null) {
            return new ViewModel();
        }

        $extendTime = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.reservation_extend_time');

        if ($booking->getExpirationDate()) {
            $date = clone $booking->getExpirationDate();
            $booking->setExpirationDate($date->add(new DateInterval($extendTime)));
            $this->getEntityManager()->flush();
            // Send an email notification indicating the assignment was extended
            BookingMail::sendAssignmentExtendedMail(
                $this->getEntityManager(),
                $this->getMailTransport(),
                [$booking],
                $booking->getPerson()
            );
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            'The booking was successfully extended and a notification email has been sent!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'edit',
                'id'     => $booking->getId(),
            )
        );

        return new ViewModel();
    }

    public function returnAction()
    {
        $booking = $this->getBookingEntity();
        if ($booking === null) {
            return new ViewModel();
        }

        if ($booking->getStatus() != 'sold') {
            return new ViewModel();
        }

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->getLast();

        $queueItem = new QueueItem($this->getEntityManager(), $booking->getPerson(), $session);
        $queueItem->setStatus('sold');
        $this->getEntityManager()->persist($queueItem);

        if (is_numeric($this->getParam('number')) && $this->getParam('number') < $booking->getNumber()) {
            $number = $this->getParam('number');
        } else {
            $number = 1;
        }

        $saleItem = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findOneByPersonAndArticle($booking->getPerson(), $booking->getArticle());

        if ($saleItem) {
            $price = $saleItem->getPrice() / $saleItem->getNumber();
        } else {
            $price = $booking->getArticle()->getSellPrice();
        }

        if ($booking->getNumber() > 1) {
            $remainder = new Booking(
                $this->getEntityManager(),
                $booking->getPerson(),
                $booking->getArticle(),
                'returned',
                $number,
                true
            );
            $this->getEntityManager()->persist($remainder);

            $booking->setNumber($booking->getNumber() - $number)
                ->setStatus('sold', $this->getEntityManager());
        } else {
            $booking->setStatus('returned', $this->getEntityManager());
        }

        for ($i = 0; $i < $number; $i++) {
            $this->getEntityManager()->persist(new ReturnItem($booking->getArticle(), $price / 100, $queueItem));
        }

        $booking->getArticle()->setStockValue($booking->getArticle()->getStockValue() + $number);

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            '<b>' . $number . '</b> items of this booking were successfully returned!'
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'edit',
                'id'     => $booking->getId(),
            )
        );

        return new ViewModel();
    }

    public function deleteAllAction()
    {
        $excluded = explode(',', $this->getParam('string'));

        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->cancelAll($this->getAuthentication()->getPersonObject(), $this->getParam('type') == 'remove_registration', $excluded);

        if ($number == 0) {
            $message = 'No booking could be removed!';
        } elseif ($number == 1) {
            $message = 'There is <b>one</b> booking removed!';
        } else {
            $message = 'There are <b>' . $number . '</b> bookings removed!';
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            $message
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function assignAllAction()
    {
        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->assignAll($this->getAuthentication()->getPersonObject(), $this->getMailTransport());

        if ($number == 0) {
            $message = 'No booking could be assigned!';
        } elseif ($number == 1) {
            $message = 'There is <b>one</b> booking assigned!';
        } else {
            $message = 'There are <b>' . $number . '</b> bookings assigned!';
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            $message
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function expireAllAction()
    {
        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->expireBookings($this->getMailTransport());

        $this->getEntityManager()->flush();

        if ($number == 0) {
            $message = 'No booking could be expired!';
        } elseif ($number == 1) {
            $message = 'There is <b>one</b> booking expired!';
        } else {
            $message = 'There are <b>' . $number . '</b> bookings expired!';
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            $message
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function extendAllAction()
    {
        $date = DateTime::createFromFormat('d#m#Y H:i', $this->getParam('date') . ' 0:00');

        $number = 0;
        if ($date) {
            $number = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Booking')
                ->extendAllBookings($date);

            $this->getEntityManager()->flush();
        }

        if ($number == 0) {
            $message = 'No booking could be extended!';
        } elseif ($number == 1) {
            $message = 'There is <b>one</b> booking extended!';
        } else {
            $message = 'There are <b>' . $number . '</b> bookings extended!';
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            $message
        );

        $this->redirect()->toRoute(
            'cudi_admin_sales_booking',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function searchAction()
    {
        $this->initAjax();

        $activePeriod = $this->getPeriodEntity();
        if ($activePeriod === null) {
            return new ViewModel();
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $bookings = $this->search($activePeriod, $this->getParam('type'))
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($bookings as $booking) {
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
        $form = $this->getForm('cudi_sale_booking_person');

        $person = $this->getAcademicEntity(true);
        if ($person !== null) {
            $paginator = $this->paginator()->createFromQuery(
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByPersonAndAcademicYearQuery($person, $this->getAcademicYearEntity()),
                $this->getParam('page')
            );

            return new ViewModel(
                array(
                    'form'              => $form,
                    'paginator'         => $paginator,
                    'paginationControl' => $this->paginator()->createControl(),
                    'person'            => $person,
                )
            );
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function articleAction()
    {
        $form = $this->getForm('cudi_sale_booking_article');

        $article = $this->getSaleArticleEntity(true);
        if ($article !== null) {
            $paginator = $this->paginator()->createFromQuery(
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByArticleAndAcademicYearQuery($article, $this->getAcademicYearEntity()),
                $this->getParam('page')
            );

            return new ViewModel(
                array(
                    'form'                => $form,
                    'currentAcademicYear' => $this->getAcademicYearEntity(),
                    'paginator'           => $paginator,
                    'paginationControl'   => $this->paginator()->createControl(),
                    'article'             => $article,
                )
            );
        }

        return new ViewModel(
            array(
                'form'                => $form,
                'currentAcademicYear' => $this->getAcademicYearEntity(),
            )
        );
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
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function undoAction()
    {
        $log = $this->getLogEntity();
        if ($log === null) {
            return new ViewModel();
        }

        if ($log->getType() == 'assigments') {
            $ids = $log->getAssigments();
            foreach ($ids as $id) {
                $booking = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneById($id);
                $booking->setStatus('booked', $this->getEntityManager());
            }
        } elseif ($log->getType() == 'cancellations') {
            $ids = $log->getCancellations();
            foreach ($ids as $id) {
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

    /**
     * @param  Period $activePeriod
     * @param  string $type
     * @return \Doctrine\ORM\Query|null
     */
    private function search(Period $activePeriod, $type)
    {
        switch ($this->getParam('field')) {
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

    /**
     * @return Period|null
     */
    private function getPeriodEntity()
    {
        if ($this->getParam('period') === null) {
            return $this->getActiveStockPeriodEntity();
        }

        $period = $this->getEntityById('CudiBundle\Entity\Stock\Period', 'period');

        if (!($period instanceof Period)) {
            $this->flashMessenger()->error(
                'Error',
                'No period was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $period;
    }

    /**
     * @return Booking|null
     */
    private function getBookingEntity()
    {
        $booking = $this->getEntityById('CudiBundle\Entity\Sale\Booking');

        if (!($booking instanceof Booking)) {
            $this->flashMessenger()->error(
                'Error',
                'No booking was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $booking;
    }

    /**
     * @param  boolean $nullable
     * @return Academic|null
     */
    private function getAcademicEntity($nullable = false)
    {
        $academic = $this->getEntityById('CommonBundle\Entity\User\Person\Academic');

        if (!($academic instanceof Academic)) {
            if ($nullable) {
                return null;
            }

            $this->flashMessenger()->error(
                'Error',
                'No academic was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academic;
    }

    /**
     * @param  boolean $nullable
     * @return SaleArticle|null
     */
    private function getSaleArticleEntity($nullable = false)
    {
        $article = $this->getEntityById('CudiBundle\Entity\Sale\Article');

        if (!($article instanceof SaleArticle)) {
            if ($nullable) {
                return null;
            }

            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $article;
    }

    /**
     * @return Log|null
     */
    private function getLogEntity()
    {
        $log = $this->getEntityById('CudiBundle\Entity\Log');

        if (!($log instanceof Log)) {
            $this->flashMessenger()->error(
                'Error',
                'No log was found!'
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_booking',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $log;
    }
}
