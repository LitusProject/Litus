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

namespace TicketBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    DateTime,
    TicketBundle\Component\Document\Generator\Event\Csv as CsvGenerator,
    TicketBundle\Component\Document\Generator\Event\Pdf as PdfGenerator,
    TicketBundle\Entity\Event,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * TicketController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TicketController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($event = $this->getEventEntity())) {
            return new ViewModel();
        }

        if (null !== $this->getParam('field')) {
            $tickets = $this->search($event);
        }

        if (!isset($tickets)) {
            $tickets = $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findAllActiveByEvent($event);
        }

        $paginator = $this->paginator()->createFromArray(
            $tickets,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'event'             => $event,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function exportAction()
    {
        if (!($event = $this->getEventEntity())) {
            return new ViewModel();
        }

        $file = new CsvFile();
        $document = new CsvGenerator($this->getEntityManager(), $event);
        $document->generateDocument($file);

        $now = new DateTime();
        $filename = 'tickets_' . $now->format('Y_m_d') . '.pdf';

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Type'        => 'text/csv',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function printAction()
    {
        if (!($event = $this->getEventEntity())) {
            return new ViewModel();
        }

        $file = new TmpFile();
        $document = new PdfGenerator($this->getEntityManager(), $event, $file);
        $document->generate();

        $now = new DateTime();
        $filename = 'tickets_' . $now->format('Y_m_d') . '.pdf';

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Type'        => 'application/pdf',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        if (!($event = $this->getEventEntity())) {
            return new ViewModel();
        }

        $tickets = $this->search($event);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($tickets, $numResults);

        $result = array();
        foreach ($tickets as $ticket) {
            $item = (object) array();
            $item->id = $ticket->getId();
            $item->person = $ticket->getFullName() ? $ticket->getFullName() : '(none)';
            $item->status = $ticket->getStatus();
            $item->option = ($ticket->getOption() ? $ticket->getOption()->getName() : '') . ' ' . ($ticket->isMember() ? 'Member' : 'Non Member');
            $item->number = $ticket->getNumber();
            $item->bookDate = $ticket->getBookDate() ? $ticket->getBookDate()->format('d/m/Y H:i') : '';
            $item->soldDate = $ticket->getSoldDate() ? $ticket->getSoldDate()->format('d/m/Y H:i') : '';
            $item->isMember = $ticket->isMember();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @param  Event      $event
     * @return array|null
     */
    private function search(Event $event)
    {
        switch ($this->getParam('field')) {
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndPersonName($event, $this->getParam('string'));
            case 'option':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndOption($event, $this->getParam('string'));
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndOrganization($event, $this->getParam('string'), $this->getCurrentAcademicYear());
        }
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
