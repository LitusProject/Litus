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

namespace TicketBundle\Component\Ticket;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\EntityManager;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\Event\Option;
use TicketBundle\Entity\GuestInfo;
use TicketBundle\Entity\Ticket as TicketEntity;

/**
 * Ticket
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Ticket
{
    /**
     * @param  Event          $event
     * @param  array          $numbers
     * @param  boolean        $payed
     * @param  string|null    $payid
     * @param  EntityManager  $entityManager
     * @param  Person|null    $person
     * @param  GuestInfo|null $guestInfo
     * @return array
     */
    public static function book(Event $event, $numbers, $payed, $payid, EntityManager $entityManager, Person $person = null, GuestInfo $guestInfo = null)
    {
        if ($event->areTicketsGenerated()) {
            return self::assignTickets($event, $numbers, $payed, $payid, $entityManager, $person, $guestInfo);
        } else {
            return self::createTickets($event, $numbers, $payed, $payid, $entityManager, $person, $guestInfo);
        }
    }

    /**
     * @param  Event          $event
     * @param  array          $numbers
     * @param  boolean        $payed
     * @param  string|null    $payid
     * @param  EntityManager  $entityManager
     * @param  Person|null    $person
     * @param  GuestInfo|null $guestInfo
     * @return array
     */
    private static function assignTickets(Event $event, $numbers, $payed, $payid, EntityManager $entityManager, Person $person = null, GuestInfo $guestInfo = null)
    {
        $createdTickets = array();
        $tickets = $entityManager->getRepository('TicketBundle\Entity\Ticket')
            ->findAllEmptyByEvent($event);

        if (count($event->getOptions()) == 0) {
            $number = $numbers['member'];
            $count = count($tickets);
            for ($i = 0; $i < $count; $i++) {
                if ($number == 0) {
                    break;
                }

                $number--;
                $tickets[$i]->setPerson($person)
                    ->setGuestInfo($guestInfo)
                    ->setMember(true)
                    ->setStatus($payed ? 'sold' : 'booked')
                    ->setPayId($payid);
                $createdTickets[] = $tickets[$i];
            }

            if (!$event->isOnlyMembers()) {
                $number = $numbers['non_member'];
                $count = count($tickets);
                for (; $i < $count; $i++) {
                    if ($number == 0) {
                        break;
                    }

                    $number--;
                    $tickets[$i]->setPerson($person)
                        ->setGuestInfo($guestInfo)
                        ->setMember(false)
                        ->setStatus($payed ? 'sold' : 'booked')
                        ->setPayId($payid);
                    $createdTickets[] = $tickets[$i];
                }
            }
        } else {
            foreach ($event->getOptions() as $option) {
                $number = $numbers['option_' . $option->getId() . '_number_member'];
                $count = count($tickets);
                for ($i = 0; $i < $count; $i++) {
                    if ($number == 0) {
                        break;
                    }

                    $number--;
                    $tickets[$i]->setPerson($person)
                        ->setGuestInfo($guestInfo)
                        ->setMember(true)
                        ->setOption($option)
                        ->setStatus($payed ? 'sold' : 'booked')
                        ->setPayId($payid);
                    $createdTickets[] = $tickets[$i];
                }

                if (!$event->isOnlyMembers()) {
                    $number = $numbers['option_' . $option->getId() . '_number_non_member'];
                    $count = count($tickets);
                    for (; $i < $count; $i++) {
                        if ($number == 0) {
                            break;
                        }

                        $number--;
                        $tickets[$i]->setPerson($person)
                            ->setGuestInfo($guestInfo)
                            ->setMember(false)
                            ->setOption($option)
                            ->setStatus($payed ? 'sold' : 'booked')
                            ->setPayId($payid);
                        $createdTickets[] = $tickets[$i];
                    }
                }
            }
        }

        return $createdTickets;
    }

    /**
     * @param  Event          $event
     * @param  array          $numbers
     * @param  boolean        $payed
     * @param  string|null    $payid
     * @param  EntityManager  $entityManager
     * @param  Person|null    $person
     * @param  GuestInfo|null $guestInfo
     * @return array
     */
    private static function createTickets(Event $event, $numbers, $payed, $payid, EntityManager $entityManager, Person $person = null, GuestInfo $guestInfo = null)
    {
        $createdTickets = array();
        if (count($event->getOptions()) == 0) {
            for ($i = 0; $i < $numbers['member']; $i++) {
                $ticket = self::createTicket($event, true, $payed, $payid, $entityManager, $person, $guestInfo, null);
                $entityManager->persist($ticket);
                $entityManager->flush();
                $createdTickets[] = $ticket;
            }

            if (!$event->isOnlyMembers()) {
                for ($i = 0; $i < $numbers['non_member']; $i++) {
                    $ticket = self::createTicket($event, false, $payed, $payid, $entityManager, $person, $guestInfo, null);
                    $entityManager->persist($ticket);
                    $entityManager->flush();
                    $createdTickets[] = $ticket;
                }
            }
        } else {
            foreach ($event->getOptions() as $option) {
                $count = $numbers['option_' . $option->getId() . '_number_member'];
                for ($i = 0; $i < $count; $i++) {
                    $ticket = self::createTicket($event, true, $payed, $payid, $entityManager, $person, $guestInfo, $option);
                    $entityManager->persist($ticket);
                    $entityManager->flush();
                    $createdTickets[] = $ticket;
                }

                if (!$event->isOnlyMembers()) {
                    $count = $numbers['option_' . $option->getId() . '_number_non_member'];
                    for ($i = 0; $i < $count; $i++) {
                        $ticket = self::createTicket($event, false, $payed, $payid, $entityManager, $person, $guestInfo, $option);
                        $entityManager->persist($ticket);
                        $entityManager->flush();
                        $createdTickets[] = $ticket;
                    }
                }
            }
        }

        return $createdTickets;
    }

    /**
     * @param  Event          $event
     * @param  boolean        $member
     * @param  boolean        $payed
     * @param  string|null    $payid
     * @param  EntityManager  $entityManager
     * @param  Person|null    $person
     * @param  GuestInfo|null $guestInfo
     * @param  Option|null    $option
     * @return TicketEntity
     */
    private static function createTicket(Event $event, $member, $payed, $payid, EntityManager $entityManager, Person $person = null, GuestInfo $guestInfo = null, Option $option = null)
    {
        $ticket = new TicketEntity(
            $entityManager,
            $event,
            'empty',
            $person,
            $guestInfo,
            null,
            null,
            $event->generateTicketNumber($entityManager)
        );
        $ticket->setMember($member)
            ->setStatus($payed ? 'sold' : 'booked')
            ->setOption($option)
            ->setPayId($payid);

        return $ticket;
    }
}
