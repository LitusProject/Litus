<?php

namespace SecretaryBundle\Controller;

use CommonBundle\Component\Controller\Exception\RuntimeException;
use TicketBundle\Component\Payment\PaymentParam;
use TicketBundle\Component\Ticket\Ticket as TicketBook;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\GuestInfo;
use TicketBundle\Entity\Ticket;
use Laminas\View\Model\ViewModel;

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

        $person = $this->getPersonEntity();

        if ($person === null) {
            $form = $this->getForm('secretary_pull_guest', array('event' => $event));
        } else {
            $form = $this->getForm('secretary_pull_account', array('event' => $event, 'person' => $person));
        }
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
}