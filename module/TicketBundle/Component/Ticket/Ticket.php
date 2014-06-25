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

namespace TicketBundle\Component\Ticket;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    TicketBundle\Entity\Event,
    TicketBundle\Entity\GuestInfo,
    TicketBundle\Entity\Option,
    TicketBundle\Entity\Ticket as TicketEntity;

/**
 * Ticket
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Ticket
{
    /**
     * @param Event $event
     * @param Person|null $person
     * @param GuestInfo|null $guestInfo
     * @param array $numbers
     * @param bool $payed
     * @param EntityManager $entityManager
     */
    public static function book(Event $event, Person $person = null, GuestInfo $guestInfo = null, $numbers, $payed, EntityManager $entityManager)
    {
        if ($event->areTicketsGenerated()) {
            $tickets = $entityManager->getRepository('TicketBundle\Entity\Ticket')
                ->findAllEmptyByEvent($event);

            if (count($event->getOptions()) == 0) {
                $number = $numbers['member'];
                for ($i = 0 ; $i < count($tickets) ; $i++) {
                    if (0 == $number)
                        break;

                    $number--;
                    $tickets[$i]->setPerson($person)
                        ->setGuestInfo($guestInfo)
                        ->setMember(true)
                        ->setStatus($payed ? 'sold' : 'booked');
                }

                if (!$event->isOnlyMembers()) {
                    $number = $numbers['non_member'];
                    for (; $i < count($tickets) ; $i++) {
                        if (0 == $number)
                            break;

                        $number--;
                        $tickets[$i]->setPerson($person)
                            ->setGuestInfo($guestInfo)
                            ->setMember(false)
                            ->setStatus($payed ? 'sold' : 'booked');
                    }
                }
            } else {
                foreach ($event->getOptions() as $option) {
                    $number = $numbers['option_' . $option->getId() . '_number_member'];
                    for ($i = 0; $i < count($tickets) ; $i++) {
                        if (0 == $number)
                            break;

                        $number--;
                        $tickets[$i]->setPerson($person)
                            ->setGuestInfo($guestInfo)
                            ->setMember(true)
                            ->setOption($option)
                            ->setStatus($payed ? 'sold' : 'booked');
                    }

                    if (!$event->isOnlyMembers()) {
                        $number = $numbers['option_' . $option->getId() . '_number_non_member'];
                        for (; $i < count($tickets) ; $i++) {
                            if (0 == $number)
                                break;

                            $number--;
                            $tickets[$i]->setPerson($person)
                                ->setGuestInfo($guestInfo)
                                ->setMember(false)
                                ->setOption($option)
                                ->setStatus($payed ? 'sold' : 'booked');
                        }
                    }
                }
            }
        } else {
            if (count($event->getOptions()) == 0) {
                for ($i = 0 ; $i < $numbers['member'] ; $i++) {
                    $entityManager->persist(
                        self::_createTicket($event, $person, $guestInfo, true, $payed, null, $entityManager)
                    );
                }

                if (!$event->isOnlyMembers()) {
                    for ($i = 0 ; $i < $numbers['non_member'] ; $i++) {
                        $entityManager->persist(
                            self::_createTicket($event, $person, $guestInfo, false, $payed, null, $entityManager)
                        );
                    }
                }
            } else {
                foreach ($event->getOptions() as $option) {
                    for ($i = 0 ; $i < $numbers['option_' . $option->getId() . '_number_member'] ; $i++) {
                        $entityManager->persist(
                            self::_createTicket($event, $person, $guestInfo, true, $payed, $option, $entityManager)
                        );
                    }

                    if (!$event->isOnlyMembers()) {
                        for ($i = 0 ; $i < $numbers['option_' . $option->getId() . '_number_non_member'] ; $i++) {
                            $entityManager->persist(
                                self::_createTicket($event, $person, $guestInfo, false, $payed, $option, $entityManager)
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Event $event
     * @param Person|null $person
     * @param GuestInfo|null $guestInfo
     * @param bool $member
     * @param bool $payed
     * @param Option|null $option
     * @param EntityManager $entityManager
     */
    private static function _createTicket(Event $event, Person $person = null, GuestInfo $guestInfo = null, $member, $payed, Option $option = null, EntityManager $entityManager)
    {
        $ticket = new TicketEntity(
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
            ->setOption($option);

        return $ticket;
    }
}
