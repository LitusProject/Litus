<?php

namespace TicketBundle\Hydrator;

use TicketBundle\Entity\Event as EventEntity;
use TicketBundle\Entity\Event\Option as OptionEntity;
use TicketBundle\Entity\Ticket as TicketEntity;

class Event extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[]
     */
    private static $stdKeys = array(
        'active', 'bookable_praesidium', 'bookable', 'number_of_tickets',
        'limit_per_person', 'only_members', 'description', 'qr_enabled', 'mail_from',
    );

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            do {
                $rand_id = uniqid();
            } while (!is_null($this->getEntityManager()
                ->getRepository("TicketBundle\Entity\Event")
                ->findOneBy(array(
                    "rand_id" => $rand_id,
                ))));
            $object = new EventEntity($rand_id);
        }

        $enableOptions = (isset($data['enable_options']) && $data['enable_options']) || count($object->getOptions()) > 0;

        try {
            $calendarEvent = $this->getEntityManager()
                ->getRepository('CalendarBundle\Entity\Node\Event')
                ->findOneById($data['event']);
        } catch (\Exception $e) {
            $calendarEvent = null;
        }

        if ($data['form'] !== '') {
            $form = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form')
                ->findOneById($data['form']);
        }
        $closeDate = self::loadDateTime($data['bookings_close_date']);

        $priceMembers = 0;
        $priceNonMembers = 0;

        if ($enableOptions) {
            foreach ($data['options'] as $optionData) {
                if (strlen($optionData['option']) == 0) {
                    continue;
                }
                error_log(json_encode($optionData['limit_per_person_option']));
                if (isset($optionData['option_id']) && is_numeric($optionData['option_id'])) {
                    $option = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Event\Option')
                        ->findOneById($optionData['option_id']);
                    $option->setName($optionData['option'])
                        ->setPriceMembers($optionData['price_members'])
                        ->setMaximum(intval($optionData['maximum']));
                    $price_non_members = $optionData['membershipDiscount'] == 1 ? $optionData['price_non_members'] : null;
                    $option->setPriceNonMembers($price_non_members);
                    $option->setIsVisible($optionData['visible']);
                    $option->setLimitPerPerson($optionData['limit_per_person_option']);
                } else {
                    $price_non_members = $optionData['membershipDiscount'] == 1 ? $optionData['price_non_members'] : null;
                    $option = new OptionEntity(
                        $object,
                        $optionData['option'],
                        $optionData['price_members'],
                        $price_non_members,
                        intval($optionData['maximum']),
                        $optionData['visible'],
                        $optionData['limit_per_person_option'],
                    );
                    $this->getEntityManager()->persist($option);
                }
            }
        } else {
            foreach ($object->getOptions() as $option) {
                $this->getEntityManager()->remove($option);
            }

            $priceMembers = $data['prices']['price_members'];
            $priceNonMembers = $data['only_members'] ? 0 : $data['prices']['price_non_members'];
        }

        $generateTickets = $data['generate_tickets'];

        if ($data['generate_tickets']) {
            if ($object->areTicketsGenerated()) {
                if ($data['number_of_tickets'] >= $object->getNumberOfTickets()) {
                    for ($i = $object->getNumberOfTickets(); $i < $data['number_of_tickets']; $i++) {
                        $this->getEntityManager()->persist(
                            new TicketEntity(
                                $this->getEntityManager(),
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
                } else {
                    // net ticket count =< old ticket count
                    $tickets = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Ticket')
                        ->findAllEmptyByEventQuery($object)->getResult();
                    $numberOfTickets = $object->getNumberOfTickets() - $data['number_of_tickets'];

                    foreach ($tickets as $ticket) {
                        if ($numberOfTickets == 0) {
                            break;
                        }

                        $numberOfTickets--;
                        $this->getEntityManager()->remove($ticket);
                    }
                }
            } else {
                // tickets weren't generated yet, but are now
                for ($i = 0; $i < $data['number_of_tickets']; $i++) {
                    $this->getEntityManager()->persist(
                        new TicketEntity(
                            $this->getEntityManager(),
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
        } elseif ($object->getId()) {
            // tickets are not generated (anymore)
            $tickets = $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findAllEmptyByEventQuery($object)->getResult();
            foreach ($tickets as $ticket) {
                $this->getEntityManager()->remove($ticket);
            }
        }


        $object->setActivity($calendarEvent)
            ->setBookingsCloseDate($closeDate)
            ->setTicketsGenerated($generateTickets)
            ->setPriceMembers($priceMembers)
            ->setPriceNonMembers($priceNonMembers)
            ->setAllowRemove($data['allow_remove'])
            ->setInvoiceIdBase($data['invoice_base_id'])
            ->setOnlinePayment($data['online_payment'])
            ->setOrderIdBase($data['order_base_id'])
            ->setForm($form)
            ->setPayDeadline($data['deadline_enabled'])
            ->setDeadlineTime($data['deadline_time'] ? : null);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['event'] = $object->getActivity()->getId();
        $data['form'] = $object->getForm() ? $object->getForm()->getId() : '';
        $data['bookings_close_date'] = $object->getBookingsCloseDate() ? $object->getBookingsCloseDate()->format('d/m/Y H:i') : '';
        $data['generate_tickets'] = $object->areTicketsGenerated();
        $data['allow_remove'] = $object->allowRemove();
        $data['deadline_enabled'] = $object->getPayDeadline();
        $data['deadline_time'] = $object->getDeadlineTime() ? : '';
        $data['invoice_base_id'] = $object->getInvoiceIdBase();
        $data['order_base_id'] = $object->getOrderIdBase();
        $data['online_payment'] = $object->isOnlinePayment();
        if (count($object->getOptions()) == 0) {
            $data['prices']['price_members'] = number_format($object->getPriceMembers() / 100, 2);
            $data['prices']['price_non_members'] = $object->isOnlyMembers() ? '' : number_format($object->getPriceNonMembers() / 100, 2);
        } else {
            $data['enable_options'] = true;
            $data['enable_options_hidden'] = '1';

            foreach ($object->getOptions() as $option) {
                $data['options'][] = array(
                    'option_id'         => $option->getId(),
                    'option'            => $option->getName(),
                    'maximum'           => $option->getMaximum(),
                    'price_members'     => number_format($option->getPriceMembers() / 100, 2),
                    'price_non_members' => $object->isOnlyMembers() ? '' : number_format($option->getPriceNonMembers() / 100, 2),
                    'membershipDiscount' => $option->getPriceNonMembers() > 0,
                    'visible'           => $option->isVisible() ? $option->isVisible() : '',
                    'limit_per_person_option' => $option->getLimitPerPerson() ? : 0,
                );
            }
        }

        return $data;
    }
}
