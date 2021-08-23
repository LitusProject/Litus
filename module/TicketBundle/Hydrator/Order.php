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

use http\Exception\RuntimeException;
use TicketBundle\Entity\OrderEntity as OrderEntity;
use TicketBundle\Entity\Ticket as TicketEntity;
use DateTime;

class Order extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'TicketBundle\Entity\OrderEntity';

    private $event;

    private $person;

    private $currentYear;

    //TODO: Change for editing
    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object || !($object instanceof OrderEntity)) {
            $object = new OrderEntity();
        }
        $currentDateTime = new DateTime();

        $object->setEvent($this->event);

        $object->setBooker($this->person);

        $object->setBookDate($currentDateTime);

        $category_map = $this->getCategoryMap();

        // Booker's ticket
        $booker_ticket = new TicketEntity();
        $booker_ticket->setStatus('booked');
        $booker_ticket->setOrderEntity($object);
        $booker_ticket->setPerson($this->person);
        $booker_ticket->setBookDate($currentDateTime);

        $booker_status = $this->person->getOrganizationStatus($this->currentYear)->getStatus();
        $booker_category = $category_map[$booker_status];
        $option_nb = $data['bookers_form']['options_select'];
        $booker_option = $booker_category->getOptions()[$option_nb];
        $booker_ticket->setOption($booker_option);

        $this->getEntityManager()->persist($booker_ticket);
        $object->addTicket($booker_ticket);


        // Guest tickets
        $academic_repo = $this->getEntityManager()->getRepository("CommonBundle\Entity\User\Person\Academic");

        $guest_form = $data['guest_form'];
        for ($i = 0; $i < count($guest_form); ++$i) {
            $guest_info = $guest_form['guest_form_' . $i];
            $r_number = $guest_info['r-number'];
            if ($r_number == "") continue;

            if (!($person = $academic_repo->findAllByUniversityIdentificationQuery($r_number)->getOneOrNullResult())) {
                return null;
            }

            $guest_ticket = new TicketEntity();
            $guest_ticket->setStatus('booked');
            $guest_ticket->setOrderEntity($object);
            $guest_ticket->setPerson($person);
            $guest_ticket->setBookDate($currentDateTime);

            $guest_category = $category_map[$person->getOrganizationStatus($this->currentYear)->getStatus()];
            $option_nb = $guest_info['options_select'];
            $guest_option = $guest_category->getOptions()[$option_nb];
            $guest_ticket->setOption($guest_option);

            $object->addTicket($guest_ticket);
            $this->getEntityManager()->persist($guest_ticket);
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        return array();
    }

    public function setEvent($event) {
        $this->event = $event;
    }

    public function setPerson($person) {
        $this->person = $person;
    }

    public function setCurrentYear($currentYear) {
        $this->currentYear = $currentYear;
    }

    public function getCategoryMap() {
        $categories = array();
        foreach ($this->event->getBookingCategories() as $category) {
            $categories[$category->getCategory()] = $category;
        }
        return $categories;
    }
}
