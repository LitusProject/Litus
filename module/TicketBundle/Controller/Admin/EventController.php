<?php

namespace TicketBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use TicketBundle\Component\Document\Generator\Event\SalesgraphCsv as SalesgraphCsvGenerator;
use TicketBundle\Entity\Event;

/**
 * EventController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EventController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Event')
                ->findAllQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Event')
                ->findOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('ticket_event_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $event = $form->hydrateObject();

                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The event was successfully created!'
                );

                $this->redirect()->toRoute(
                    'ticket_admin_event',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $form = $this->getForm('ticket_event_edit', array('event' => $event));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The event was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'ticket_admin_event',
                    array(
                        'action' => 'edit',
                        'id'     => $event->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'      => $form,
                'event'     => $event,
                'em'        => $this->getEntityManager(),
                'info_form' => $event->getForm(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $event->setActive(false);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function cleanAction()
    {
        $event = $this->getEventEntity();

        $payTime = $event->getDeadlineTime();

        $bookedNotSold = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllByStatusAndEvent('booked', $event);

        foreach ($bookedNotSold as $expired) {
            $now = new \DateTime('now');
            $book_date = $expired->getBookDate();
            $time_diff = $now->getTimestamp() - $book_date->getTimeStamp();

            if ($event->getPayDeadline()) {
                // In this case, people can pay their ticket longer than 24 hours.
                // We clean all tickets older than an hour
                $days = $time_diff / (24 * 60 * 60); // Set Time Difference in seconds to day
                if ($days > 1) {
                    $this->getEntityManager()->remove($expired);
                }
            } else {
                // Here, we check how long the pay time is
                // We clean all tickets that are older than the pay deadline
                $time = $time_diff / (60); // Set Time Diff from seconds to minutes
                if ($time > $payTime) {
                    $this->getEntityManager()->remove($expired);
                }
            }
        }
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'ticket_admin_event',
            array(
                'action' => 'edit',
                'id'     => $event->getId(),
            )
        );

        return new ViewModel();
    }

    /**
     * Show a graph with sales data
     */
    public function salesgraphAction()
    {
        $sales = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllByStatusAndEvent('sold', $this->getEventEntity());

        // $data exist of a key: UNIX timestamp and the amount of tickets sold at that moment
        $data = array();
        foreach ($sales as $sale) {
            if (array_key_exists($sale->getSoldDate()->format('Uv'), $data)) {
                $data[$sale->getSoldDate()->format('Uv')]++;
            } else {
                $data[$sale->getSoldDate()->format('Uv')] = 1;
            }
        }


        $dates = array();
        $sales_each_day = array();
        foreach ($data as $date => $nb_of_sales) {
            $dates[] = $date;
            $sales_each_day[] = $nb_of_sales;
        }

        $sales_accumulated = array();
        for ($i = 0; $i < sizeof($sales_each_day); $i++) {
            $sales_accumulated[$i] = array_sum(array_slice($sales_each_day, 0, $i));
        }

        $salesGraphData['labels'] = $dates;
        $salesGraphData['dataset'] = $sales_accumulated;

        return new ViewModel(
            array(
                'event'          => $this->getEventEntity(),
                'salesGraphData' => $salesGraphData,
            )
        );
    }

    /**
     * Export the data of the salesgraph
     */
    public function exportSalesgraphAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $sales = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllByStatusAndEvent('sold', $this->getEventEntity());

        // $data exist of a key: UNIX timestamp and the amount of tickets sold at that moment
        $data = array();
        foreach ($sales as $sale) {
            if (array_key_exists($sale->getSoldDate()->format('Uv'), $data)) {
                $data[$sale->getSoldDate()->format('Uv')]++;
            } else {
                $data[$sale->getSoldDate()->format('Uv')] = 1;
            }
        }

        $dates = array();
        $sales_each_day = array();
        foreach ($data as $date => $nb_of_sales) {
            $dates[] = $date;
            $sales_each_day[] = $nb_of_sales;
        }

        $sales_accumulated = array();
        for ($i = 0; $i < sizeof($sales_each_day); $i++) {
            $sales_accumulated[$i] = array_sum(array_slice($sales_each_day, 0, $i));
        }

        $salesGraphData['labels'] = $dates;
        $salesGraphData['dataset'] = $sales_accumulated;

        $file = new CsvFile();
        $document = new SalesgraphCsvGenerator($salesGraphData);
        $document->generateDocument($file);

        $now = new DateTime();
        $filename = 'salesgraph_' . str_replace(' ', '_', $event->getActivity()->getTitle()) . '_'. $now->format('Y_m_d') . '.csv';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    /**
     * Clear all visitors of this event. This will set the exit_time of every visitor to now.
     */
    public function clearVisitorsAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $visitors = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event\Visitor')
            ->findAllByEventAndExitNull($event);

        foreach ($visitors as $visitor) {
            $visitor->setExitTimestamp(new DateTime());
        }
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'All the visitors of this event have been removed!'
        );

        $this->redirect()->toRoute(
            'ticket_admin_event',
            array(
                'action' => 'edit',
                'id'     => $event->getId(),
            )
        );
        return new ViewModel();
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('TicketBundle\Entity\Event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'ticket_admin_event',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }
}
