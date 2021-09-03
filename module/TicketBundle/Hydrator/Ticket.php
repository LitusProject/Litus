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
            $guestInfo = new GuestInfo($data['guest_form']['guest_first_name'], $data['guest_form']['guest_last_name'], $data['guest_form']['guest_email'], $data['guest_form']['guest_organization']);
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
