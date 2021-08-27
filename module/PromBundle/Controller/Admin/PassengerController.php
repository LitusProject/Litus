<?php

namespace PromBundle\Controller\Admin;

use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;
use PromBundle\Entity\Bus\Passenger;

/**
 * PassengerController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class PassengerController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus\Passenger')
                ->findAllPassengersByAcademicYearFirstBus($this->getCurrentAcademicYear()),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAction()
    {
        $passenger = $this->getPassengerEntity();
        if ($passenger === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($passenger);
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'prom_admin_passenger',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function removeBusAction()
    {
        $passenger = $this->getPassengerEntity();
        if ($passenger === null) {
            return new ViewModel();
        }

        $bus = $passenger->getFirstBus();
        $passenger->setFirstBus(null);
        $passenger->setSecondBus(null);

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('prom.remove_mail')
        );

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(str_replace('{{ busTime }}', $bus->getDepartureTime()->format('d/m/Y H:i'), $mailData['body']))
            ->setFrom($mailData['from'])
            ->addTo($passenger->getEmail())
            ->addBcc($mailData['from'])
            ->setSubject($mailData['subject']);

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }

        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'prom_admin_bus',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    /**
     * @return Passenger|null
     */
    private function getPassengerEntity()
    {
        $passenger = $this->getEntityById('PromBundle\Entity\Bus\Passenger');

        if (!($passenger instanceof Passenger)) {
            $this->flashMessenger()->error(
                'Error',
                'No passenger was found!'
            );

            $this->redirect()->toRoute(
                'prom_admin_passenger',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $passenger;
    }
}
