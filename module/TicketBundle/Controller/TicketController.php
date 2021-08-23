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

namespace TicketBundle\Controller;

use Laminas\View\Model\ViewModel;
use TicketBundle\Component\Payment\PaymentParam;
use TicketBundle\Component\Ticket\Ticket as TicketBook;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\Ticket;

/**
 * TicketController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TicketController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function eventAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return $this->notFoundAction();
        }

        $person = $this->getPersonEntity();
        if ($person === null) {
            return $this->notFoundAction();
        }

        $tickets = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllByEventAndPerson($event, $person);

        if (count($tickets) >= $event->getLimitPerPerson() || $event->getNumberFree() <= 0){
            $canBook = false;
        }
        $form = $this->getForm('ticket_ticket_book', array('event' => $event, 'person' => $person));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $numbers = array(
                    'member' => $formData['number_member'] ?? 0,
                    'non_member' => $formData['number_non_member'] ?? 0,
                );

                foreach ($event->getOptions() as $option) {
                    $numbers['option_' . $option->getId() . '_number_member'] = $formData['option_' . $option->getId() . '_number_member'];
                    $numbers['option_' . $option->getId() . '_number_non_member'] = $formData['option_' . $option->getId() . '_number_non_member'];
                }

                TicketBook::book(
                    $event,
                    $numbers,
                    false,
                    $this->getEntityManager(),
                    $person,
                    null
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The tickets were succesfully booked'
                );

                $this->redirect()->toRoute(
                    'ticket',
                    array(
                        'action' => 'event',
                        'id' => $event->getId(),
                    )
                );
            }
        }

        $organizationStatus = $person->getOrganizationStatus($this->getCurrentAcademicYear());

        return new ViewModel(
            array(
                'event'                 => $event,
                'tickets'               => $tickets,
                'form'                  => $form,
                'canRemoveReservations' => $event->canRemoveReservation($this->getEntityManager()),
                'isPraesidium'          => $organizationStatus ? $organizationStatus->getStatus() == 'praesidium' : false,
                'canBook'               => $canBook,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $ticket = $this->getTicketEntity();
        if ($ticket === null) {
            return $this->notFoundAction();
        }

        if ($ticket->getEvent()->areTicketsGenerated()) {
            $ticket->setStatus('empty');
        } else {
            $this->getEntityManager()->remove($ticket);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function payAction()
    {
        $ticket = $this->getTicketEntity();
        if ($ticket === null) {
            return $this->notFoundAction();
        }

        $secretInfo = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.kbc_secret_info');

        $shaIn = $secretInfo['shaIn']; #Hash for params to the paypage
        $shaOut = $secretInfo['shaOut']; #Hash for params from the paypage to accepturl
        $urlPrefix = $secretInfo['urlPrefix'];   #Change prod to test for testenvironment

        $url = $this->url()->fromRoute(
            'ticket',
            array(
                'action' => 'event',
                'id' => $ticket->getEvent()->getId(),
            )
        );

        $price = $ticket->getOption()->getPriceNonMembers();
        if ($ticket->isMember() === true){
            $price = $ticket->getOption()->getPriceMembers();
        }
        $mail = $ticket->getPerson()->getEmail();
        if ($ticket->getGuestInfo() !== null) {
            $mail = $ticket->getGuestInfo()->getEmail();
        }

        // TODO: AcceptUrl, COM, OrderID and shaOut!!
        $data = [   #These are in alphabetical order as that is required for the hash
            new PaymentParam("ACCEPTURL", $url), #URL where user is redirected to when payment is accepted, the same parameters that were sent to paypage will be returned, and hashed (sha-512) to check for validity. (https://support-paypage.ecom-psp.com/en/integration-solutions/integrations/hosted-payment-page#e_commerce_integration_guides_transaction_feedback)
            new PaymentParam("AMOUNT", $price ), #Required, in cents
            new PaymentParam("CN", $ticket->getFullName() ),
            new PaymentParam("COM", "700100 2022-001-0001" ),  #Required for beheer: char 0-15 given by beheer, last 4 should increment with each payment
            new PaymentParam("COMPLUS", $ticket->getOption()->getName() ),  #Comment
            new PaymentParam("CURRENCY", "EUR" ),  #Required
            new PaymentParam("EMAIL", $mail ),
            new PaymentParam("LANGUAGE", "nl_NL" ),
            new PaymentParam("LOGO", "logo.png" ), #Required
            new PaymentParam("ORDERID", "20220010001" ), #Required, char 0-6 given by beheer, last 4 should increment with each payment
            new PaymentParam("PMLISTTYPE", "2" ), #Required
            new PaymentParam("PSPID", "vtkprod" ), #Required
            new PaymentParam("TP", "ingenicoResponsivePaymentPageTemplate_index.html" ), #Required
        ];

        $data_filtered = array_filter($data, "PaymentParam::nonEmptyPaymentParam"); #No empty params in url/hash
        $paymentUrl = PaymentParam::getUrl($data_filtered, $shaIn, $urlPrefix);
        $this->redirect()->toUrl($paymentUrl);

        $ticket->setStatus('payed');

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The ticket was succesfully payed for!'
        );

        $this->redirect()->toRoute(
            'ticket',
            array(
                'action' => 'event',
                'id' => $ticket->getEvent()->getId(),
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('TicketBundle\Entity\Event');

        if (!($event instanceof Event) || !$event->isActive()) {
            return;
        }

        return $event;
    }

    /**
     * @return Ticket|null
     */
    private function getTicketEntity()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return;
        }

        $ticket = $this->getEntityById('TicketBundle\Entity\Ticket');

        if (!($ticket instanceof Ticket) || $ticket->getPerson() != $person) {
            return;
        }

        return $ticket;
    }
}
