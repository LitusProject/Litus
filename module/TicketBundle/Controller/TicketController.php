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

use CommonBundle\Component\Controller\Exception\RuntimeException;
use CommonBundle\Component\Form\Admin\Element\DateTime;
use FormBundle\Entity\Node\Entry as FormEntry;
use FormBundle\Entity\Node\Form;
use Laminas\Mail\Message;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Laminas\View\Model\ViewModel;
use TicketBundle\Component\Payment\PaymentParam;
use TicketBundle\Component\Ticket\Ticket as TicketBook;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\GuestInfo;
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

        $formSpecification = $this->getFormEntity($event->getForm());
        if ($formSpecification !== null) {
            $now = new DateTime();
            if ($now < $formSpecification->getStartDate() || $now > $formSpecification->getEndDate() || !$formSpecification->isActive()) {
                return new ViewModel(
                    array(
                        'message'       => 'This form is currently closed.',
                        'specification' => $formSpecification,
                    )
                );
            }
            $guestInfo = null;
            $entries = null;

            if ($person !== null) {
                $entries = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findAllByFormAndPerson($formSpecification, $person);
            } elseif ($this->isCookieSet()) {
                $guestInfo = $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\GuestInfo')
                    ->findOneBySessionId($this->getCookie());

                if ($guestInfo) {
                    $entries = $this->getEntityManager()
                        ->getRepository('FormBundle\Entity\Node\Entry')
                        ->findAllByFormAndGuestInfo($formSpecification, $guestInfo);
                }
            }

            if ($person === null && !$formSpecification->isNonMember()) {
                return new ViewModel(
                    array(
                        'message'   => 'Please login to view this form',
                        'specification' => $formSpecification,
                    )
                );
            } elseif (!$formSpecification->isMultiple() && count($entries) > 0) {
                return new ViewModel(
                    array(
                        'message' => 'You can\'t fill this form more than once',
                        'specification' => $formSpecification,
                        'entries' => $entries,
                    )
                );
            }

            $entriesCount = count(
                $this->getEntityManager()
                    ->getRepository('FormBundle\Entity\Node\Entry')
                    ->findAllByForm($formSpecification)
            );

            if ($formSpecification->getMax() != 0 && $entriesCount >= $formSpecification->getMax()) {
                return new ViewModel(
                    array(
                        'message'       => 'This form has reached the maximum number of submissions.',
                        'specification' => $formSpecification,
                        'entries'       => $entries,
                    )
                );
            }

            $infoForm = $this->getForm(
                'form_specified-form_add',
                array(
                    'form'       => $formSpecification,
                    'person'     => $person,
                    'language'   => $this->getLanguage(),
                    'guest_info' => $guestInfo,
                    'event'      => $event,
                    'is_event_form' => true,
                )
            );
        }

        if ($person === null) {
            $canBook = true;
            if ($event->getNumberOfTickets() != 0 && $event->getNumberFree() <= 0) {
                $canBook = false;
            }
            $form = $this->getForm('ticket_ticket_bookguest', array('event' => $event));

            if ($this->getRequest()->isPost()) {
                if ($infoForm !== null) {
                    $infoForm->setData(
                        array_merge_recursive(
                            $this->getRequest()->getPost()->toArray(),
                            $this->getRequest()->getFiles()->toArray()
                        )
                    );

                    if ($infoForm->isValid()) {
                        $formEntry = new FormEntry($formSpecification, $person);
                        if ($person === null) {
                            $formEntry->setGuestInfo(
                                new \FormBundle\Entity\Node\GuestInfo($this->getEntityManager(), $this->getRequest())
                            );
                        }
                        $formEntry = $infoForm->hydrateObject($formEntry);
                        $this->getEntityManager()->persist($formEntry);

                        $formData = $infoForm->getData();
                        $numbers = array(
                            'member'     => $formData['number_member'] ?? 0,
                            'non_member' => $formData['number_non_member'] ?? 0,
                        );

                        foreach ($event->getOptions() as $option) {
                            $numbers['option_' . $option->getId() . '_number_member'] = $formData['option_' . $option->getId() . '_number_member'];
                            $numbers['option_' . $option->getId() . '_number_non_member'] = $formData['option_' . $option->getId() . '_number_non_member'];
                            $currentAmount = count($this->getEntityManager()->getRepository('TicketBundle\Entity\Ticket')->findAllByOption($option));
                            $currentAmount += $numbers['option_' . $option->getId() . '_number_member'];
                            $currentAmount += $numbers['option_' . $option->getId() . '_number_non_member'];
                            $selectedAmount = $numbers['option_' . $option->getId() . '_number_member'] + $numbers['option_' . $option->getId() . '_number_non_member'];
                            if ($option->getMaximum() != 0 && $currentAmount > $option->getMaximum() && $selectedAmount > 0) {
                                $this->flashMessenger()->error(
                                    'Error',
                                    'The tickets could not be booked, option "' . $option->getName() . '" has reached the maximum amount of  tickets!'
                                );
                                $this->redirect()->toRoute(
                                    'ticket',
                                    array(
                                        'action'  => 'event',
                                        'id' => $event->getRandId(),
                                    )
                                );
                                return new ViewModel();
                            }
                        }

                        $guestInfo = new GuestInfo(
                            $formData['first_name'],
                            $formData['last_name'],
                            $formData['email'],
                            $formData['organization'],
                            $formData['identification'],
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null
                        );

                        $this->getEntityManager()->persist($guestInfo);
                        $this->getEntityManager()->flush();

                        $booked_tickets = TicketBook::book(
                            $event,
                            $numbers,
                            false,
                            $this->getEntityManager(),
                            null,
                            $guestInfo
                        );

                        $this->getEntityManager()->flush();

                        if ($guestInfo === null) {
                            throw new RuntimeException('Guestinfo is null');
                        }

                        if($event->isOnlinePayment()){
                            foreach ($booked_tickets as $ticket) {
                                $this->sendMail($ticket);
                            }
                        }

                        $this->flashMessenger()->success(
                            'Success',
                            'The tickets were succesfully booked'
                        );

                        $this->redirect()->toRoute(
                            'ticket',
                            array(
                                'action' => 'event',
                                'id'     => $event->getRandId(),
                            )
                        );
                    }
                } else {
                    $form->setData(
                        array_merge_recursive(
                            $this->getRequest()->getPost()->toArray(),
                            $this->getRequest()->getFiles()->toArray(),
                        )
                    );

                    $filePath = 'public/_ticket/img';

                    if ($form->isValid()) {
                        $formData = $form->getData();

                        $numbers = array(
                            'member'     => $formData['number_member'] ?? 0,
                            'non_member' => $formData['number_non_member'] ?? 0,
                        );

                        foreach ($event->getOptions() as $option) {
                            $numbers['option_' . $option->getId() . '_number_member'] = $formData['option_' . $option->getId() . '_number_member'];
                            $numbers['option_' . $option->getId() . '_number_non_member'] = $formData['option_' . $option->getId() . '_number_non_member'];
                            $currentAmount = count($this->getEntityManager()->getRepository('TicketBundle\Entity\Ticket')->findAllByOption($option));
                            $currentAmount += $numbers['option_' . $option->getId() . '_number_member'];
                            $currentAmount += $numbers['option_' . $option->getId() . '_number_non_member'];
                            $selectedAmount = $numbers['option_' . $option->getId() . '_number_member'] + $numbers['option_' . $option->getId() . '_number_non_member'];
                            if ($option->getMaximum() != 0 && $currentAmount > $option->getMaximum() && $selectedAmount > 0) {
                                $this->flashMessenger()->error(
                                    'Error',
                                    'The tickets could not be booked, option "' . $option->getName() . '" has reached the maximum amount of tickets!'
                                );
                                $this->redirect()->toRoute(
                                    'ticket',
                                    array(
                                        'action' => 'event',
                                        'id'     => $event->getRandId(),
                                    )
                                );
                                return new ViewModel();
                            }
                        }

                        $guestInfo = new GuestInfo(
                            $formData['guest_form']['guest_first_name'],
                            $formData['guest_form']['guest_last_name'],
                            $formData['guest_form']['guest_email'],
                            $formData['guest_form']['guest_organization'],
                            $formData['guest_form']['guest_identification'],
                        );

                        if ($formData['guest_form']['picture']) {
                            $image = new \Imagick($formData['guest_form']['picture']['tmp_name']);
                        }

                        do {
                            $newFileName = sha1(uniqid());
                        } while (file_exists($filePath . '/' . $newFileName));

                        //$image->writeImage($filePath . '/' . $newFileName);
                        //$guestInfo->setPicture($newFileName);

                        $this->getEntityManager()->persist($guestInfo);
                        $this->getEntityManager()->flush();

                        $booked_tickets = TicketBook::book(
                            $event,
                            $numbers,
                            false,
                            $this->getEntityManager(),
                            null,
                            $guestInfo
                        );

                        // if guestinfo (idk of deze nodig is)
                        // foreach ticket in generated tickets (nie gwn tickets)
                        //      send mail to ticket.getGuestInfo().getEMail()
                        //      Met de generatePayUrl($ticket) en mss een kleine uitleg

                        $this->getEntityManager()->flush();

                        if ($guestInfo === null) {
                            throw new RuntimeException('Guestinfo is null');
                        }

                        if($event->isOnlinePayment()){
                            foreach ($booked_tickets as $ticket) {
                                $this->sendMail($ticket);
                            }
                        }

                        $this->flashMessenger()->success(
                            'Success',
                            'The tickets were succesfully booked'
                        );

                        $this->redirect()->toRoute(
                            'ticket',
                            array(
                                'action' => 'event',
                                'id'     => $event->getRandId(),
                            )
                        );
                    }
//                $form->setData($this->getRequest()->getPost());
                }
            }
            return new ViewModel(
                array(
                    'event'                 => $event,
                    'tickets'               => array(),
                    'form'                  => $form,
                    'canRemoveReservations' => $event->canRemoveReservation($this->getEntityManager()),
                    'isOnlinePayment'       => $event->isOnlinePayment(),
                    'isPraesidium'          => false,
                    'canBook'               => $canBook,
                    'maximumAmount'         => $event->getLimitPerPerson(),
                    'upperText'             => unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('ticket.upper_text')
                    )[$this->getLanguage()->getAbbrev()],
                    'isGuest'               => true,
                    'specification'         => $formSpecification,
                    'infoform'              => $infoForm ?: false,
                    'entries'               => $entries ?: null,
                )
            );
        } else {
            $tickets = $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findAllByEventAndPerson($event, $person);

            $canBook = true;
            if ((count($tickets) >= $event->getLimitPerPerson() && $event->getLimitPerPerson() != 0) || ($event->getNumberFree() <= 0 && $event->getNumberOfTickets() != 0)) {
                $canBook = false;
            }
            $form = $this->getForm('ticket_ticket_book', array('event' => $event, 'person' => $person));

            if ($this->getRequest()->isPost()) {
                if ($infoForm !== null) {
                    $infoForm->setData(
                        array_merge_recursive(
                            $this->getRequest()->getPost()->toArray(),
                            $this->getRequest()->getFiles()->toArray()
                        )
                    );

                    if ($infoForm->isValid()) {
                        $formEntry = new FormEntry($formSpecification, $person);
                        if ($person === null) {
                            $formEntry->setGuestInfo(
                                new \FormBundle\Entity\Node\GuestInfo($this->getEntityManager(), $this->getRequest())
                            );
                        }
                        $formEntry = $infoForm->hydrateObject($formEntry);
                        $this->getEntityManager()->persist($formEntry);

                        $formData = $infoForm->getData();
                        $numbers = array(
                            'member'     => $formData['number_member'] ?? 0,
                            'non_member' => $formData['number_non_member'] ?? 0,
                        );

                        foreach ($event->getOptions() as $option) {
                            $numbers['option_' . $option->getId() . '_number_member'] = $formData['option_' . $option->getId() . '_number_member'];
                            $numbers['option_' . $option->getId() . '_number_non_member'] = $formData['option_' . $option->getId() . '_number_non_member'];
                            $currentAmount = count($this->getEntityManager()->getRepository('TicketBundle\Entity\Ticket')->findAllByOption($option));
                            $currentAmount += $numbers['option_' . $option->getId() . '_number_member'];
                            $currentAmount += $numbers['option_' . $option->getId() . '_number_non_member'];
                            $selectedAmount = $numbers['option_' . $option->getId() . '_number_member'] + $numbers['option_' . $option->getId() . '_number_non_member'];
                            if ($option->getMaximum() != 0 && $currentAmount > $option->getMaximum() && $selectedAmount > 0) {
                                $this->flashMessenger()->error(
                                    'Error',
                                    'The tickets could not be booked, option "' . $option->getName() . '" has reached the maximum amount of tickets!'
                                );
                                $this->redirect()->toRoute(
                                    'ticket',
                                    array(
                                        'action' => 'event',
                                        'id'     => $event->getRandId(),
                                    )
                                );
                                return new ViewModel();
                            }
                        }

                        $booked_tickets = TicketBook::book(
                            $event,
                            $numbers,
                            false,
                            $this->getEntityManager(),
                            $person,
                            null
                        );

                        $this->getEntityManager()->flush();

                        if($event->isOnlinePayment()){
                            foreach ($booked_tickets as $ticket) {
                                $this->sendMail($ticket);
                            }
                        }

                        $this->flashMessenger()->success(
                            'Success',
                            'The tickets were succesfully booked'
                        );

                        $this->redirect()->toRoute(
                            'ticket',
                            array(
                                'action' => 'event',
                                'id'     => $event->getRandId(),
                            )
                        );
                    }
                } else {
                    $form->setData($this->getRequest()->getPost());

                    if ($form->isValid()) {
                        $formData = $form->getData();

                        $numbers = array(
                            'member'     => $formData['number_member'] ?? 0,
                            'non_member' => $formData['number_non_member'] ?? 0,
                        );

                        foreach ($event->getOptions() as $option) {
                            $numbers['option_' . $option->getId() . '_number_member'] = $formData['option_' . $option->getId() . '_number_member'];
                            $numbers['option_' . $option->getId() . '_number_non_member'] = $formData['option_' . $option->getId() . '_number_non_member'];
                            $currentAmount = count($this->getEntityManager()->getRepository('TicketBundle\Entity\Ticket')->findAllByOption($option));
                            $currentAmount += $numbers['option_' . $option->getId() . '_number_member'];
                            $currentAmount += $numbers['option_' . $option->getId() . '_number_non_member'];
                            $selectedAmount = $numbers['option_' . $option->getId() . '_number_member'] + $numbers['option_' . $option->getId() . '_number_non_member'];
                            if ($option->getMaximum() != 0 && $currentAmount > $option->getMaximum() && $selectedAmount > 0) {
                                $this->flashMessenger()->error(
                                    'Error',
                                    'The tickets could not be booked, option "' . $option->getName() . '" has reached the maximum amount of tickets!'
                                );
                                $this->redirect()->toRoute(
                                    'ticket',
                                    array(
                                        'action' => 'event',
                                        'id'     => $event->getRandId(),
                                    )
                                );
                                return new ViewModel();
                            }
                        }

                        $booked_tickets = TicketBook::book(
                            $event,
                            $numbers,
                            false,
                            $this->getEntityManager(),
                            $person,
                            null
                        );

                        $this->getEntityManager()->flush();

                        if($event->isOnlinePayment()){
                            foreach ($booked_tickets as $ticket) {
                                $this->sendMail($ticket);
                            }
                        }

                        $this->flashMessenger()->success(
                            'Success',
                            'The tickets were succesfully booked'
                        );

                        $this->redirect()->toRoute(
                            'ticket',
                            array(
                                'action' => 'event',
                                'id'     => $event->getRandId(),
                            )
                        );
                    }
                }
            }
        }

        $organizationStatus = $person->getOrganizationStatus($this->getCurrentAcademicYear());

        return new ViewModel(
            array(
                'event'                 => $event,
                'tickets'               => $tickets,
                'form'                  => $form,
                'canRemoveReservations' => $event->canRemoveReservation($this->getEntityManager()),
                'isOnlinePayment'       => $event->isOnlinePayment(),
                'isPraesidium'          => $organizationStatus ? $organizationStatus->getStatus() == 'praesidium' : false,
                'canBook'               => $canBook,
                'maximumAmount'         => $event->getLimitPerPerson(),
                'upperText'             => unserialize(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('ticket.upper_text')
                )[$this->getLanguage()->getAbbrev()],
                'isGuest'               => false,
                'specification'         => $formSpecification,
                'infoform'              => $infoForm,
                'entries'               => $entries,
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
        } elseif ($ticket->getStatusCode() !== 'sold') {
            $this->getEntityManager()->remove($ticket);
        } else {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
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
                'ticket',
                array(
                    'action' => 'event',
                    'id'     => $ticket->getEvent()->getRandId(),
                )
            );
        } else {
            $ticket->setStatus('sold');
            if ($ticket->getEvent()->getQrEnabled()) {
                $ticket->setQrCode();
            }
            $this->getEntityManager()->flush();
            if ($ticket->getEvent()->getQrEnabled()) {
                $this->sendQrMail($ticket);
            }

            $this->flashMessenger()->success(
                'Success',
                'The ticket was successfully paid for!'
            );

            $this->redirect()->toRoute(
                'ticket',
                array(
                    'action' => 'event',
                    'id'     => $ticket->getEvent()->getRandId(),
                )
            );
        }
        return new ViewModel();
    }

    public function payResponseAction()
    {
        $printerEventId = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.printer_event_id');

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

        if ($paymentParams['ORDERID'] === null) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        $ticket = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findOneBy(
                array(
                    'orderId' => $paymentParams['ORDERID']
                )
            );

        ksort($paymentParams);
        foreach (array_keys($paymentParams) as $paymentKey) {
            $data[] = new PaymentParam($paymentKey, $paymentParams[$paymentKey]);
        }

        $secretInfo = unserialize(
            $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.kbc_secret_info')
        );

        $shaOut = $secretInfo['shaOut']; #Hash for params from the paypage to accepturl
        $urlPrefix = $secretInfo['urlPrefix'];   #Change prod to test for testenvironment

        $paymentUrl = PaymentParam::getUrl($data, $shaOut, $urlPrefix);
        $generatedHash = substr($paymentUrl, strpos($paymentUrl, 'SHASIGN=') + strlen('SHASIGN='));

        if (strtoupper($generatedHash) === $shasign) {
            if (!($ticket->getStatus() == 'Sold')) {
                $ticket->setStatus('sold');
                if ($ticket->getEvent()->getQrEnabled()) {
                    $ticket->setQrCode();
                    $this->sendQrMail($ticket);
                }
                if ($ticket->getEvent()->getId() === $printerEventId) {
//                    $this->runPowershell($ticket);
                }
                $this->getEntityManager()->flush();
            }
        } else {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        return new ViewModel();
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

        if ($ticket->getNumber() !== $this->getParam('code')) {
            return new \ErrorException('This paylink contains the wrong ticket code...');
        }

        $now = new \DateTime('now');
        $book_date = $ticket->getBookDate();
        $time_diff = $now->getTimestamp() - $book_date->getTimeStamp();
        $time_in_minutes = $time_diff/(60); // Set Time Difference in seconds to minutes

        $max_time = $ticket->getEvent()->getDeadlineTime();

        if ($time_in_minutes <= $max_time || $ticket->getEvent()->getPayDeadline()) {
            $link = $this->generatePayLink($ticket);

            $this->redirect()->toUrl($link);
        } else {
            return new ViewModel(
                array(
                    'late' => true,
                    'event' => $ticket->getEvent(),
                )
            );
        }
    }

    public function qrAction()
    {
        $qr = $this->getParam('qr');
        if ($qr === null) {
            return new ViewModel();
        }

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $ticket = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findOneByQREvent($event, $qr)[0];

        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();
        }

        if ($person !== null) {
            // Check if person has access to scan QR
            if ($this->hasAccess()->toResourceAction('ticket', 'scanQr')) {
                $visitor = $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Event\Visitor')
                    ->findByEventAndQrAndExitNull($event, $qr);

                if ($visitor == null) {
                    // If no visitor, first entry
                    $entry = true;

                    $visitor = new Event\Visitor($event, $qr);
                    $this->getEntityManager()->persist($visitor);
                    $this->getEntityManager()->flush();
                } else {
                    // If visitor already exists, can't enter again
                    $entry = false;
                }
                return new ViewModel(
                    array(
                        'event' => $event,
                        'entry' => $entry,
                        'ticket_option' => $ticket->getOption() ? $ticket->getOption()->getName() : 'default',
                        'ticket' => $ticket,
                    )
                );
            }
        }

        // From here, only when not authorized to scan QR

        $encodedUrl = urlencode(
            $this->url()
                ->fromRoute(
                    'ticket',
                    array('action' => 'qr',
                        'id' => $event->getRandId(),
                        'qr' => $qr
                    ),
                    array('force_canonical' => true)
                )
        );

        $encodedUrl = str_replace('leia.', '', $encodedUrl);
        $encodedUrl = str_replace('liv.', '', $encodedUrl);

        $qrSource = str_replace(
            '{{encodedUrl}}',
            $encodedUrl,
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.google_qr_api')
        );

        return new ViewModel(
            array(
                'event'    => $event,
                'qrSource' => $qrSource,
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
        $event = $this->getEntityById('TicketBundle\Entity\Event', 'id', 'rand_id');
        if (!($event instanceof Event)) {
            // Events without a rand_id return id when calling event->getRandId(). We only want to find events by there id
            // if they don't have a rand_id. This is because older events don't have a rand_id.
            $event = $this->getEntityManager()->getRepository('TicketBundle\Entity\Event')
                ->findOneBy(
                    array(
                        'id' => $this->getParam('id'),
                        'rand_id' => null,
                    )
                );
            if (!($event instanceof Event)) {
                return;
            }
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

    /**
     * @param Ticket $ticket
     * @return string
     */
    private function generatePayLink(Ticket $ticket)
    {
        $secretInfo = unserialize(
            $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.kbc_secret_info')
        );

        $shaIn = $secretInfo['shaIn']; #Hash for params to the paypage
        $urlPrefix = $secretInfo['urlPrefix'];   #Change prod to test for testenvironment

        $url = 'https://vtk.be' . $this->url()->fromRoute(
            'ticket',
            array(
                'action' => 'payed',
                'id'     => $ticket->getId(),
                'code'   => $ticket->getNumber(),
            )
        );

        $priceHolder = $ticket->getOption() ?? $ticket->getEvent();
        $price = $priceHolder->getPriceNonMembers();
        if ($ticket->isMember() === true) {
            $price = $priceHolder->getPriceMembers();
        }

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

    private function sendMail(Ticket $ticket)
    {
        $mail = new Message();

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('ticket.confirmation_email_body')
        );

        $eventName = $ticket->getEvent()->getActivity()->getTitle();

        $mailBody = $ticket->getEvent()->getConfirmationMailBody() ? :$mailData['content'];
        $mailSubject = $ticket->getEvent()->getConfirmationMailSubject() ? :$mailData['subject'];

        $mailFrom = $ticket->getEvent()->getMailFrom() ? :$this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.confirmation_email_from');

        if ($ticket->getPerson()) {
            $person = $ticket->getPerson();
            $fullName = $person->getFullName();
            $mailTo = $person->getEmail();
        } elseif ($ticket->getGuestInfo()) {
            $guest = $ticket->getGuestInfo();
            $fullName = $guest->getFullName();
            $mailTo = $guest->getEmail();
        } else {
            throw new RuntimeException('attempted to send email for ticket without Person or GuestInfo');
        }

        $optionString = 'Standaardticket';
        if ($ticket->getOption()) {
            $option = $ticket->getOption();
            $optionName = $option->getName();
            if ($ticket->isMember()) {
                $optionString = $optionName . ' - lid';
            } else {
                $optionString = $optionName . ' - niet-lid';
            }
        }

        $payLinkDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.pay_link_domain');
        $payLink = $payLinkDomain . '/en/ticket/pay/' . $ticket->getId() . '/code/' . $ticket->getNumber();

        $mail->setEncoding('UTF-8')
            ->setBody(str_replace(array('{{ fullname }}', '{{ event }}', '{{ option }}', '{{ paylink }}'), array($fullName, $eventName, $optionString, $payLink), $mailBody))
            ->setFrom($mailFrom)
            ->addTo($mailTo)
            ->addBcc($mailFrom)
            ->addBcc('it@vtk.be')
            ->setSubject(str_replace('{{ event }}', $eventName, $mailSubject));

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }

    private function getFormEntity($formId)
    {
        $form = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findOneById($formId);

        if (!($form instanceof Form)) {
            return;
        }

        $form->setEntityManager($this->getEntityManager());

        return $form;
    }

    /**
     * @return boolean
     */
    private function isCookieSet()
    {
        $cookie = $this->getRequest()->getCookie();

        return $cookie !== false && $cookie->offsetExists(\FormBundle\Entity\Node\GuestInfo::$cookieNamespace);
    }

    /**
     * @return string
     */
    private function getCookie()
    {
        $cookie = $this->getRequest()->getCookie();

        return $cookie !== false && $cookie->offsetExists(\FormBundle\Entity\Node\GuestInfo::$cookieNamespace);
    }

    private function sendQrMail(Ticket $ticket)
    {
        $event = $ticket->getEvent();
        $language = $this->getLanguage();

        $entityManager = $this->getEntityManager();
        if ($language === null) {
            $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('ticket.subscription_mail_data')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = str_replace('{{event}}', $event->getActivity()->getTitle($language), $mailData[$language->getAbbrev()]['subject']);

        $mailAddress = $ticket->getEvent()->getMailFrom() ? :$this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.subscription_mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.subscription_mail_name');

        $url = $this->url()
            ->fromRoute(
                'ticket',
                array('action' => 'qr',
                    'id'       => $event->getRandId(),
                    'qr'     => $ticket->getQrCode()
                ),
                array('force_canonical' => true)
            );

        $url = str_replace('leia.', '', $url);

        $qrSource = str_replace(
            '{{encodedUrl}}',
            urlencode($url),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.google_qr_api')
        );

        $message = str_replace('{{event}}', $event->getActivity()->getTitle($language), $message);
        $message = str_replace('{{eventDate}}', $event->getActivity()->getStartDate()->format('d/m/Y'), $message);
        $message = str_replace('{{qrSource}}', $qrSource, $message);
        $message = str_replace('{{qrLink}}', $url, $message);
        $message = str_replace('{{actiMail}}', $mailAddress, $message);
        $message = str_replace('{{ticketOption}}', $ticket->getOption() ? $ticket->getOption()->getName() : 'base', $message);

        $part = new Part($message);

        $part->type = Mime::TYPE_HTML;
        $part->charset = 'utf-8';
        $newMessage = new \Laminas\Mime\Message();
        $newMessage->addPart($part);
        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($newMessage)
            ->setFrom($mailAddress, $mailName)
            ->addTo($ticket->getEmail(), $ticket->getFullName())
            ->setSubject($subject)
            ->addBcc('it@vtk.be')
            ->addBcc($mailAddress);
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }

    private function runPowershell($ticket)
    {
        $scriptPath = getcwd() . '/module/CudiBundle/Resources/bin/uniflow.ps1';
        $universityMail = $ticket->getUniversityMail();
        $amount = $ticket->getAmount();
        $clientId = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.printer_uniflow_client_id');
        $clientSecret = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.printer_uniflow_client_secret');

        $command = 'pwsh ' . " " . $scriptPath . " '". $clientId . "' '" . $clientSecret . "' '" . $universityMail . "' '" . $amount . "'";

        try {
            $query = shell_exec("$command 2>&1");
        } catch (\Exception $e) {
            $this->getSentryClient()->logException($e);
        }
    }
}
