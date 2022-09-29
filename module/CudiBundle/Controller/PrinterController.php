<?php

namespace CudiBundle\Controller;

use CommonBundle\Component\Controller\Exception\RuntimeException;
use TicketBundle\Component\Payment\PaymentParam;
use TicketBundle\Component\Ticket\Ticket as TicketBook;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\GuestInfo;
use TicketBundle\Entity\Ticket;
use Laminas\View\Model\ViewModel;

class PrinterController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $eventId = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.printer_event_id');

        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($eventId);

        if ($event === null) {
            return $this->notFoundAction();
        }
        $person = $this->getPersonEntity();

        if ($person === null) {
            $form = $this->getForm('cudi_printer_buyguest', array('event' => $event));

            if ($this->getRequest()->isPost()) {
                $form->setData($this->getRequest()->getPost());

                if ($form->isValid()) {
                    $formData = $form->getData();

                    $amount = $formData['amount'];
                    $universityMail = $formData['guest_form']['guest_email'];

                    $guestInfo = new GuestInfo(
                        $formData['guest_form']['guest_first_name'],
                        $formData['guest_form']['guest_last_name'],
                        $formData['guest_form']['guest_email'],
                        null,
                        null,
                    );
                    $this->getEntityManager()->persist($guestInfo);
                    $this->getEntityManager()->flush();

                    $numbers = array(
                        'member' => 0,
                        'non_member' => 1,
                    );

                    $booked_tickets = TicketBook::book(
                        $event,
                        $numbers,
                        false,
                        $this->getEntityManager(),
                        null,
                        $guestInfo,
                    );

                    $this->getEntityManager()->flush();

                    $this->redirect()->toUrl('');
                }
            }
        } else {
            $form = $this->getForm('cudi_printer_buy', array('event' => $event, 'person' => $person));

            if ($this->getRequest()->isPost()) {
                $form->setData($this->getRequest()->getPost());

                if ($form->isValid()) {
                    $formData = $form->getData();

                    $amount = $formData['amount'];
                    $universityMail = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($person->getId())->getUniversityEmail();

                    if ($person->isMember($this->getCurrentAcademicYear())) {
                        $numbers = array(
                            'member' => 1,
                            'non_member' => 0,
                        );
                    } else {
                        $numbers = array(
                            'member' => 0,
                            'non_member' => 1,
                        );
                    }

                    $booked_ticket = TicketBook::book(
                        $event,
                        $numbers,
                        false,
                        $this->getEntityManager(),
                        $person,
                        null,
                    );
                    $booked_ticket[0]->setAmount($amount);
                    $this->getEntityManager()->flush();

                    $payLinkDomain = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('ticket.pay_link_domain');
                    $payLink = 'http://litus' . '/cudi/printer/pay/' . $booked_ticket[0]->getId() . '/code/' . $booked_ticket[0]->getNumber();

                    $this->redirect()->toUrl($payLink);
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function payAction()
    {
        $ticket = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findOneById($this->getParam('id'));

        if ($ticket === null) {
            return $this->notFoundAction();
        }

        if ($ticket->getEvent()->isOnlinePayment() === false || $ticket->getEvent()->isActive() === false) {
            return $this->notFoundAction();
        }

        $link = $this->generatePayLink($ticket);
        $this->redirect()->toUrl($link);

        return new ViewModel();
    }

    public function payedAction()
    {
        // TODO: payed Action
    }

    private function generatePayLink($ticket) {
        $secretInfo = unserialize(
            $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.kbc_secret_info')
        );

        $shaIn = $secretInfo['shaIn']; #Hash for params to the paypage
        $urlPrefix = $secretInfo['urlPrefix'];   #Change prod to test for testenvironment

        $url = 'https://vtk.be' . $this->url()->fromRoute(
                'cudi_printer',
                array(
                    'action' => 'payed',
                    'id'     => $ticket->getId(),
                    'code'   => $ticket->getNumber(),
                )
            );

        $price = $ticket->getAmount() * 100;

        if ($ticket->getPerson()) {
            $mail = $ticket->getPerson()->getEmail();
        } elseif ($ticket->getGuestInfo()) {
            $mail = $ticket->getGuestInfo()->getEmail();
        } else {
            throw new RuntimeException('attempting to generate ticket without email address, email is required');
        }

        $com = $ticket->getInvoiceId();
        $orderId = $ticket->getOrderId();

        $comment = $ticket->getOption() ? $ticket->getOption()->getName() : ($ticket->isMember() ? 'member' : 'non-member');

        // TODO: AcceptUrl and shaOut!!
        $data = array(   #These are in alphabetical order as that is required for the hash
            new PaymentParam('ACCEPTURL', $url), #URL where user is redirected to when payment is accepted, the same parameters that were sent to paypage will be returned, and hashed (sha-512) to check for validity. (https://support-paypage.ecom-psp.com/en/integration-solutions/integrations/hosted-payment-page#e_commerce_integration_guides_transaction_feedback)
            new PaymentParam('AMOUNT', $price), #Required, in cents
            new PaymentParam('CN', $ticket->getFullName()),
            new PaymentParam('COM', $com),  #Required for beheer: char 0-15 given by beheer, last 4 should increment with each payment
            new PaymentParam('COMPLUS', $comment),  #Comment
            new PaymentParam('CURRENCY', 'EUR'),  #Required
            new PaymentParam('EMAIL', $mail),
            new PaymentParam('LANGUAGE', 'nl_NL'),
            new PaymentParam('LOGO', 'logo.png'), #Required
            new PaymentParam('ORDERID', $orderId), #Required, char 0-6 given by beheer, last 4 should increment with each payment
            new PaymentParam('PMLISTTYPE', '2'), #Required
            new PaymentParam('PSPID', 'vtkprod'), #Required
            new PaymentParam('TP', 'ingenicoResponsivePaymentPageTemplate_index.html'), #Required
        );

        $newData = array();
        foreach ($data as $param) {
            if (!$param->isEmpty()) {
                array_push($newData, $param);
            }
        }
        return PaymentParam::getUrl($newData, $shaIn, $urlPrefix);
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
}