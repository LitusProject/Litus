<?php

namespace SecretaryBundle\Controller;

use CommonBundle\Component\Controller\Exception\RuntimeException;
use Laminas\Mail\Message;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Laminas\View\Model\ViewModel;
use TicketBundle\Component\Payment\PaymentParam;
use TicketBundle\Component\Ticket\Ticket as TicketBook;
use TicketBundle\Entity\GuestInfo;
use TicketBundle\Entity\Ticket;

class PullController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $eventId = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.pull_event_id');

        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($eventId);

        if ($event === null) {
            return $this->notFoundAction();
        }

        $options = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Pull')
            ->findAllAvailableQuery()
            ->getResult();

        $person = $this->getPersonEntity();

        if ($person === null) {
            $form = $this->getForm('secretary_pull_guest', array('event' => $event));

            if ($this->getRequest()->isPost()) {
                $form->setData($this->getRequest()->getPost());

                if ($form->isValid()) {
                    $formData = $form->getData();

                    $guestInfo = new GuestInfo(
                        $formData['guest_form']['guest_first_name'],
                        $formData['guest_form']['guest_last_name'],
                        $formData['guest_form']['guest_email'],
                        null,
                        null,
                    );
                    $this->getEntityManager()->persist($guestInfo);
                    $this->getEntityManager()->flush();

                    $option = $formData['pull'];

                    $pull = $this->getEntityManager()
                        ->getRepository('SecretaryBundle\Entity\Pull')
                        ->findOneById($option);

                    $numbers = array(
                        'member'     => 0,
                        'non_member' => 1,
                    );

                    $pull->addOrdered();

                    $booked_ticket = TicketBook::book(
                        $event,
                        $numbers,
                        false,
                        null,
                        $this->getEntityManager(),
                        null,
                        $guestInfo,
                    );

                    $amount = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('secretary.pull_price');

                    $booked_ticket[0]->setAmount($amount);
                    $this->getEntityManager()->flush();

                    $payLinkDomain = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('ticket.pay_link_domain');
                    $payLink = 'https://vtk.be' . '/secretary/pull/pay/' . $booked_ticket[0]->getId() . '/code/' . $booked_ticket[0]->getNumber();

                    $this->redirect()->toUrl($payLink);
                }
            }
        } else {
            $form = $this->getForm('secretary_pull_account', array('event' => $event, 'person' => $person));

            if ($this->getRequest()->isPost()) {
                $form->setData($this->getRequest()->getPost());

                if ($form->isValid()) {
                    $formData = $form->getData();

                    if ($person->isMember($this->getCurrentAcademicYear())) {
                        $numbers = array(
                            'member'     => 1,
                            'non_member' => 0,
                        );
                    } else {
                        $numbers = array(
                            'member'     => 0,
                            'non_member' => 1,
                        );
                    }

                    $option = $formData['pull'];

                    $pull = $this->getEntityManager()
                        ->getRepository('SecretaryBundle\Entity\Pull')
                        ->findOneById($option);

                    $pull->addOrdered();

                    $this->getEntityManager()->flush();

                    $booked_ticket = TicketBook::book(
                        $event,
                        $numbers,
                        false,
                        null,
                        $this->getEntityManager(),
                        $person,
                        null,
                    );
                    $amount = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('secretary.pull_price');

                    $booked_ticket[0]->setAmount($amount);

                    $this->getEntityManager()->flush();

                    $payLinkDomain = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('ticket.pay_link_domain');
                    $payLink = $payLinkDomain . '/secretary/pull/pay/' . $booked_ticket[0]->getId() . '/code/' . $booked_ticket[0]->getNumber();

                    $this->redirect()->toUrl($payLink);
                }
            }
        }

        return new ViewModel(
            array(
                'options' => $options,
                'form'    => $form,
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

    // TODO: Add mail after payed
    public function payedAction()
    {
        $ticket = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findOneById($this->getParam('id'));
        if ($ticket === null) {
            return $this->notFoundAction();
        }

        if ($ticket->getNumber() !== $this->getParam('code')) {
            return new \ErrorException('This paylink contains the wrong ticket code...');
        }

        $secretInfo = unserialize(
            $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.kbc_secret_info')
        );

        $shaOut = $secretInfo['shaOut']; #Hash for params from the paypage to accepturl
        $urlPrefix = $secretInfo['urlPrefix'];   #Change prod to test for testenvironment

        $url = $this->getRequest()->getServer()->get('REQUEST_URI');
        $allParams = substr($url, strpos($url, '?') + 1);
        $data = array();
        $paymentParams = array();
        $shasign = '';

        $params = explode('&', $allParams);
        foreach ($params as $param) {
            $keyAndVal = explode('=', $param);
            if ($keyAndVal[0] !== 'SHASIGN') {
                $paymentParams[strtoupper($keyAndVal[0])] = $keyAndVal[1];
            } else {
                $shasign = $keyAndVal[1];
            }
        }

        ksort($paymentParams);
        foreach (array_keys($paymentParams) as $paymentKey) {
            $data[] = new PaymentParam($paymentKey, $paymentParams[$paymentKey]);
        }
        $paymentUrl = PaymentParam::getUrl($data, $shaOut, $urlPrefix);
        $generatedHash = substr($paymentUrl, strpos($paymentUrl, 'SHASIGN=') + strlen('SHASIGN='));

        if (strtoupper($generatedHash) !== $shasign) {
            $this->flashMessenger()->error(
                'Error',
                'The transaction could not be verified!'
            );

            $this->redirect()->toRoute(
                'secretary_pull',
                array(
                    'action' => 'view',
                )
            );
        } else {
            $ticket->setStatus('sold');

            $this->sendMail($ticket);

            $this->flashMessenger()->success(
                'Success',
                'The ticket was successfully payed for!'
            );

            $this->redirect()->toRoute(
                'secretary_pull',
                array(
                    'action' => 'view',
                )
            );
        }
        return new ViewModel();
    }

    public function generatePayLink($ticket)
    {
        $secretInfo = unserialize(
            $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.kbc_secret_info')
        );

        $shaIn = $secretInfo['shaIn']; #Hash for params to the paypage
        $urlPrefix = $secretInfo['urlPrefix'];   #Change prod to test for testenvironment

        $url = 'https://vtk.be' . $this->url()->fromRoute(
            'secretary_pull',
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

    private function sendMail(Ticket $ticket)
    {
        $entityManager = $this->getEntityManager();
        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.pull_confirmation_mail')
        );

        $message = $mailData['content'];
        $subject = $mailData['subject'];

        $part = new Part($message);

        $part->type = Mime::TYPE_HTML;
        $part->charset = 'utf-8';
        $newMessage = new \Laminas\Mime\Message();
        $newMessage->addPart($part);
        $mail = new Message();
        $secretary_mail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('secretary.mail');
        $mail->setEncoding('UTF-8')
            ->setBody($newMessage)
            ->setFrom($secretary_mail, 'Secretaris')
            ->addTo($ticket->getEmail(), $ticket->getFullName())
            ->setSubject($subject)
            ->addBcc(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('system_administrator_mail'),
                'System Administrator'
            )
            ->addBcc($secretary_mail);
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}
