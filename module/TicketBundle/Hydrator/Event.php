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

namespace TicketBundle\Hydrator;

use DateTime,
    TicketBundle\Entity\Event as EventEntity,
    TicketBundle\Entity\Option;

class Event extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[]
     */
    private static $std_keys = array(
        'active', 'bookable_praesidium', 'bookable', 'number_of_tickets',
        'limit_per_person', 'only_members',
    );

    protected function doHydrate(array $array, $object = null)
    {
        if (null === $object) {
            $object = new EventEntity();
        }

        $enableOptions = isset($data['enable_options']) && $data['enable_options'] || sizeof($object->getOptions()) > 0;

        $calendarEvent = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneById($data['event']);

        $closeDate = self::loadDate($data['bookings_close_date']);

        $priceMembers = $enableOptions ? 0 : $data['price_members'];
        $priceNonMembers = $enableOptions ? 0 : ($data['only_members'] ? 0 : $data['price_non_members']);

        $generateTickets = $data['generate_tickets'];

        if ($enableOptions) {
            foreach ($data['options'] as $optionData) {
                if (strlen($optionData['option']) == 0)
                    continue;

                if (isset($optionData['option_id']) && is_numeric($optionData['option_id'])) {
                    $option = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Option')
                        ->findOneById($optionData['option_id']);
                    $option->setName($optionData['option'])
                        ->setPriceMembers($optionData['price_members'])
                        ->setPriceNonMembers($data['only_members'] ? 0 : $optionData['price_non_members']);
                } else {
                    $option = new Option(
                        $object,
                        $optionData['option'],
                        $optionData['price_members'],
                        $data['only_members'] ? 0 : $optionData['price_non_members']
                    );
                    $this->getEntityManager()->persist($option);
                }
            }
        } else {
            foreach ($object->getOptions() as $option) {
                $this->getEntityManager()->remove($option);
            }
        }

        if ($data['generate_tickets']) {
            if ($event->areTicketsGenerated()) {
                if ($data['number_of_tickets'] >= $object->getNumberOfTickets()) {
                    for ($i = $object->getNumberOfTickets(); $i < $data['number_of_tickets']; $i++) {
                        $this->getEntityManager()->persist(
                            new Ticket(
                                $object,
                                'empty',
                                null,
                                null,
                                null,
                                null,
                                $object->generateTicketNumber($this->getEntityManager())
                            )
                        );
                    }
                } else { // net ticket count =< old ticket count
                    $tickets = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Ticket')
                        ->findAllEmptyByEvent($object);
                    $numberOfTickets = $object->getNumberOfTickets() - $data['number_of_tickets'];

                    foreach ($tickets as $ticket) {
                        if ($numberOfTickets == 0)
                            break;

                        $numberOfTickets--;
                        $this->getEntityManager()->remove($ticket);
                    }
                }
            } else { // tickets weren't generated yet, but are now
                for ($i = 0 ; $i < $data['number_of_tickets'] ; $i++) {
                    $this->getEntityManager()->persist(
                        new Ticket(
                            $object,
                            'empty',
                            null,
                            null,
                            null,
                            null,
                            $object->generateTicketNumber($this->getEntityManager())
                        )
                    );
                }
            }
        } else { // tickets are not generated (anymore)
            $tickets = $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findAllEmptyByEvent($event);
            foreach ($tickets as $ticket) {
                $this->getEntityManager()->remove($ticket);
            }
        }

        $object->setActivity($calendarEvent)
            ->setBookingsCloseDate($closeDate)
            ->setTicketsGenerated($generateTickets)
            ->setPriceMembers($priceMembers)
            ->setPriceNonMembers($priceNonMembers);

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['event'] = $event->getActivity()->getId();
        $data['bookings_close_date'] = $event->getBookingsCloseDate() ? $event->getBookingsCloseDate()->format('d/m/Y H:i') : '';
        $data['generate_tickets'] = $event->areTicketsGenerated();
        $data['allow_remove'] = $event->allowRemove();

        if (sizeof($event->getOptions()) == 0) {
            $data['prices']['price_members'] = number_format($event->getPriceMembers()/100, 2);
            $data['prices']['price_non_members'] = $event->isOnlyMembers() ? '' : number_format($event->getPriceNonMembers()/100, 2);
        } else {
            $data['enable_options'] = true;
            $data['enable_options_hidden'] = '1';

            foreach ($event->getOptions() as $option) {
                $data['options'][] = array(
                    'option_id' => $option->getId(),
                    'option' => $option->getName(),
                    'price_members' => number_format($option->getPriceMembers()/100, 2),
                    'price_non_members' => $event->isOnlyMembers() ? '' : number_format($option->getPriceNonMembers()/100, 2),
                );
            }
        }

        return $data;
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
