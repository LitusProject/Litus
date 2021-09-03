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

        $canBook = true;
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
                    $currentAmount = $this->getEntityManager()->getRepository('TicketBundle\Entity\Ticket')->findAllByOption($option);
                    if ($currentAmount >= $option->getMaximum()){
                        $this->flashMessenger()->error(
                            'Error',
                            'The tickets could not be booked, option ' . $option->getName() . 'has reached the maximum amount of tickets!'
                        );

                    }
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
                'maximumAmount'         => $event->getLimitPerPerson(),
                'upperText'             => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('ticket.upper_text'),
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

    public function payedAction()
    {
        $ticket = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findOneById($this->getParam('id'));
        if ($ticket === null) {
            return $this->notFoundAction();
        }

        $secretInfo = unserialize($this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.kbc_secret_info'));

        $shaOut = $secretInfo['shaOut']; #Hash for params from the paypage to accepturl
        $urlPrefix = $secretInfo['urlPrefix'];   #Change prod to test for testenvironment

//         https://vtk.be/en/ticket/payed/420/?orderID=20220020052&STATUS=9&PAYID=6132815586&NCERROR=0&SHASIGN=07FE512C732A050409519E866F997C6FA1FAFC0227A8DA7DE5890770695205FF6B8BBCA45EFA9A26135F3B58E953DE132EF920F3C7BB83AC706F8465DF4C65E6

        $url = "$_SERVER[REQUEST_URI]";
        $allParams = substr($url, strpos($url, "?") + 1);
        $data = array();
        $paymentParams = array();
        $shasign = '';

        $params = explode("&",$allParams );
        foreach ($params as $param){
            $keyAndVal = explode("=", $param);
            if ($keyAndVal[0] !== 'SHASIGN'){
                $paymentParams[strtoupper($keyAndVal[0])] = $keyAndVal[1];
            }
            else {
                $shasign = $keyAndVal[1];
            }
        }

        ksort($paymentParams);
        foreach (array_keys($paymentParams) as $paymentKey){
            $data[] = new PaymentParam($paymentKey, $paymentParams[$paymentKey]);
        }
        $paymentUrl = PaymentParam::getUrl($data, $shaOut, $urlPrefix);
        $generatedHash = substr($paymentUrl, strpos($paymentUrl, "SHASIGN=") + strlen("SHASIGN="));

        if (strtoupper($generatedHash) !== $shasign){
            $this->flashMessenger()->error(
                'Error',
                'The transaction could not be verified!'
            );

            $this->redirect()->toRoute(
                'ticket',
                array(
                    'action' => 'event',
                    'id' => $ticket->getEvent()->getId(),
                )
            );
        } else {
            $ticket->setStatus('sold');

            $this->getEntityManager()->flush();

            $this->flashMessenger()->success(
                'Success',
                'The ticket was successfully payed for!'
            );

            $this->redirect()->toRoute(
                'ticket',
                array(
                    'action' => 'event',
                    'id' => $ticket->getEvent()->getId(),
                )
            );
        }
        return new ViewModel();
    }

    public function payAction()
    {
        $ticket = $this->getTicketEntity();
        if ($ticket === null) {
            return $this->notFoundAction();
        }

        if ($ticket->getEvent()->isOnlinePayment() === false){
            return $this->notFoundAction();
        }

        $secretInfo = unserialize($this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.kbc_secret_info'));

        $shaIn = $secretInfo['shaIn']; #Hash for params to the paypage
        $urlPrefix = $secretInfo['urlPrefix'];   #Change prod to test for testenvironment

        $url = "https://vtk.be" . $this->url()->fromRoute(
            'ticket',
            array(
                'action' => 'payed',
                'id' => $ticket->getId(),
            )
        );

        $priceHolder = $ticket->getOption()??$ticket->getEvent();
        $price = $priceHolder->getPriceNonMembers();
        if ($ticket->isMember() === true){
            $price = $priceHolder->getPriceMembers();
        }
        $mail = $ticket->getPerson()->getEmail();
        if ($ticket->getGuestInfo() !== null) {
            $mail = $ticket->getGuestInfo()->getEmail();
        }

        $com = $ticket->getInvoiceId();
        $orderId = $ticket->getOrderId();

        $comment = $ticket->getOption()?$ticket->getOption()->getName() : $ticket->isMember()? "member" : "non-member";

        // TODO: AcceptUrl and shaOut!!
        $data = [   #These are in alphabetical order as that is required for the hash
            new PaymentParam("ACCEPTURL", $url), #URL where user is redirected to when payment is accepted, the same parameters that were sent to paypage will be returned, and hashed (sha-512) to check for validity. (https://support-paypage.ecom-psp.com/en/integration-solutions/integrations/hosted-payment-page#e_commerce_integration_guides_transaction_feedback)
            new PaymentParam("AMOUNT", $price ), #Required, in cents
            new PaymentParam("CN", $ticket->getFullName() ),
            new PaymentParam("COM", $com ),  #Required for beheer: char 0-15 given by beheer, last 4 should increment with each payment
            new PaymentParam("COMPLUS", $comment ),  #Comment
            new PaymentParam("CURRENCY", "EUR" ),  #Required
            new PaymentParam("EMAIL", $mail ),
            new PaymentParam("LANGUAGE", "nl_NL" ),
            new PaymentParam("LOGO", "logo.png" ), #Required
            new PaymentParam("ORDERID", $orderId ), #Required, char 0-6 given by beheer, last 4 should increment with each payment
            new PaymentParam("PMLISTTYPE", "2" ), #Required
            new PaymentParam("PSPID", "vtkprod" ), #Required
            new PaymentParam("TP", "ingenicoResponsivePaymentPageTemplate_index.html" ), #Required
        ];

        $newData = array();
        foreach ($data as $param){
            if (!$param->isEmpty()){
                array_push($newData, $param);
            }
        }
        $paymentUrl = PaymentParam::getUrl($newData, $shaIn, $urlPrefix);
        $this->redirect()->toUrl($paymentUrl);

//        return new ViewModel();
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
