<?php

namespace TicketBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use TicketBundle\Component\Ticket\Ticket as TicketBook;
use TicketBundle\Entity\GuestInfo;

class Ticket extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'TicketBundle\Entity\Event';

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        if ($data['is_guest']) {
            $person = null;
            $guestInfo = new GuestInfo(
                $data['guest_form']['guest_first_name'],
                $data['guest_form']['guest_last_name'],
                $data['guest_form']['guest_email'],
                null,
                null,
            );
            $this->getEntityManager()->persist($guestInfo);
        } else {
            $person = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['person_form']['person']['id']);
            $guestInfo = null;
        }

        $numbers = array(
            'member'     => $data['options_form']['number_member'] ?? 0,
            'non_member' => $data['options_form']['number_non_member'] ?? 0,
        );

        foreach ($object->getOptions() as $option) {
            $numbers['option_' . $option->getId() . '_number_member'] = $data['options_form']['option_' . $option->getId() . '_number_member'];
            $numbers['option_' . $option->getId() . '_number_non_member'] = $data['options_form']['option_'.$option->getId().'_number_non_member'] ?? 0;
        }

        return TicketBook::book(
            $object,
            $numbers,
            $data['payed'],
            $data['payed']? $data['payId'] : null,
            $this->getEntityManager(),
            $person,
            $guestInfo
        );
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}
