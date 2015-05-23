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

namespace CudiBundle\Controller\Admin\Sale;

use CommonBundle\Entity\User\Person\Academic,
    CudiBundle\Component\Mail\Booking as BookingMail,
    CudiBundle\Entity\Log,
    CudiBundle\Entity\Sale\Article as SaleArticle,
    CudiBundle\Entity\Sale\Booking,
    CudiBundle\Entity\Sale\QueueItem,
    CudiBundle\Entity\Sale\ReturnItem,
    CudiBundle\Entity\Stock\Period,
    DateInterval,
    DateTime,
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
        if (!($currentPeriod = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (!($activePeriod = $this->getPeriodEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
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
        if (!($currentPeriod = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (!($activePeriod = $this->getPeriodEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
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
        if (!($currentPeriod = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (!($activePeriod = $this->getPeriodEntity())) {
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
        if (!($booking = $this->getBookingEntity())) {
            return new ViewModel();
        }

        if (!($currentPeriod = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        if (!($activePeriod = $this->getPeriodEntity())) {
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

        $mailForm = $this->getForm('cudi_mail_send', array(
            'email' => $booking->getPerson()->getEmail(),
            'name'  => $booking->getPerson()->getFullName(),
        ));
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
        if (!($booking = $this->getBookingEntity())) {
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
        if (!($booking = $this->getBookingEntity())) {
            return new ViewModel();
        }

        if (!($currentPeriod = $this->getActiveStockPeriodEntity())) {
            return new ViewModel();
        }

        $available = $booking->getArticle()->getStockValue() - $currentPeriod->getNbAssigned($booking->getArticle());
        if ($available <= 0) {
            $this->flashMessenger()->error(
                'Error',
                'The booking could not be assigned! Not enough articles in stock.'
            );

            $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

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

        BookingMail::sendAssignMail($this->getEntityManager(), $this->getMailTransport(), array($booking), $booking->getPerson());

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The booking was successfully assigned!'
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function unassignAction()
    {
        if (!($booking = $this->getBookingEntity())) {
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

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function expireAction()
    {
        if (!($booking = $this->getBookingEntity())) {
            return new ViewModel();
        }

        $booking->setStatus('expired', $this->getEntityManager());
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            'The booking was successfully expired!'
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function extendAction()
    {
        if (!($booking = $this->getBookingEntity())) {
            return new ViewModel();
        }

        $extendTime = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.reservation_extend_time');

        if ($booking->getExpirationDate()) {
            $date = clone $booking->getExpirationDate();
            $booking->setExpirationDate($date->add(new DateInterval($extendTime)));
            $this->getEntityManager()->flush();
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            'The booking was successfully extended!'
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function returnAction()
    {
        if (!($booking = $this->getBookingEntity()) || $booking->getStatus() != 'sold') {
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

        for ($i = 0 ; $i < $number ; $i++) {
            $this->getEntityManager()->persist(new ReturnItem($booking->getArticle(), $price/100, $queueItem));
        }

        $booking->getArticle()->setStockValue($booking->getArticle()->getStockValue() + $number);

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'SUCCESS',
            '<b>' . $number . '</b> items of this booking were successfully returned!'
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function deleteAllAction()
    {
        $excluded = explode(',', $this->getParam('string'));

        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->cancelAll($this->getAuthentication()->getPersonObject(), $this->getParam('type') == 'remove_registration', $excluded);

        if (0 == $number) {
            $message = 'No booking could be removed!';
        } elseif (1 == $number) {
            $message = 'There is <b>one</b> booking removed!';
        } else {
            $message = 'There are <b>' . $number . '</b> bookings removed!';
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            $message
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function assignAllAction()
    {
        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->assignAll($this->getAuthentication()->getPersonObject(), $this->getMailTransport());

        if (0 == $number) {
            $message = 'No booking could be assigned!';
        } elseif (1 == $number) {
            $message = 'There is <b>one</b> booking assigned!';
        } else {
            $message = 'There are <b>' . $number . '</b> bookings assigned!';
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            $message
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function expireAllAction()
    {
        $number = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->expireBookings($this->getMailTransport());

        $this->getEntityManager()->flush();

        if (0 == $number) {
            $message = 'No booking could be expired!';
        } elseif (1 == $number) {
            $message = 'There is <b>one</b> booking expired!';
        } else {
            $message = 'There are <b>' . $number . '</b> bookings expired!';
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            $message
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

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

        if (0 == $number) {
            $message = 'No booking could be extended!';
        } elseif (1 == $number) {
            $message = 'There is <b>one</b> booking extended!';
        } else {
            $message = 'There are <b>' . $number . '</b> bookings extended!';
        }

        $this->flashMessenger()->success(
            'SUCCESS',
            $message
        );

        $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));

        return new ViewModel();
    }

    public function searchAction()
    {
        $this->initAjax();

        if (!($activePeriod = $this->getPeriodEntity())) {
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

        if ($person = $this->getAcademicEntity()) {
            $paginator = $this->paginator()->createFromQuery(
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByPersonAndAcademicYearQuery($person, $this->getAcademicYearEntity()),
                $this->getParam('page')
            );

            return new ViewModel(
                array(
                    'form' => $form,
                    'paginator' => $paginator,
                    'paginationControl' => $this->paginator()->createControl(),
                    'person' => $person,
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
        if (!($activePeriod = $this->getPeriodEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('cudi_sale_booking_article');

        if ($article = $this->getSaleArticleEntity()) {
            $paginator = $this->paginator()->createFromQuery(
                $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findAllByArticleAndAcademicYearQuery($article, $this->getAcademicYearEntity()),
                $this->getParam('page')
            );

            return new ViewModel(
                array(
                    'form' => $form,
                    'currentAcademicYear' => $this->getAcademicYearEntity(),
                    'paginator' => $paginator,
                    'paginationControl' => $this->paginator()->createControl(),
                    'article' => $article,
                )
            );
        }

        return new ViewModel(
            array(
                'form' => $form,
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
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function undoAction()
    {
        if (!($log = $this->getLogEntity())) {
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
     * @param  Period                   $activePeriod
     * @param  string                   $type
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
        if (null === $this->getParam('period')) {
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
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        $academic = $this->getEntityById('CommonBundle\Entity\User\Person\Academic');

        if (!($academic instanceof Academic)) {
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
     * @return SaleArticle|null
     */
    private function getSaleArticleEntity()
    {
        $article = $this->getEntityById('CudiBundle\Entity\Sale\Article');

        if (!($article instanceof SaleArticle)) {
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
