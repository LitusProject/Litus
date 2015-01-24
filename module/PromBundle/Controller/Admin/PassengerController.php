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

namespace PromBundle\Controller\Admin;

use Zend\Mail\Message,
    Zend\View\Model\ViewModel;

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
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function deleteAction()
    {
        if (!($passenger = $this->_getPassenger())) {
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
        if (!($passenger = $this->_getPassenger())) {
            return new ViewModel();
        }

        $passenger->setBus(null);

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('prom.remove_mail')
        );

        $mail = new Message();
        $mail->setBody(str_replace('{{ busTime }}', $bus->getDepartureTime()->format('d/m/Y h:i'), $mailData['body']))
            ->setFrom($mailData['from'])
            ->addTo($passenger->getEmail())
            ->addBcc($mailData['from'])
            ->setSubject($mailData['subject']);

        if ('development' != getenv('APPLICATION_ENV')) {
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

    private function _getPassenger()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the passenger!'
            );

            $this->redirect()->toRoute(
                'prom_admin_passenger',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $passenger = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\Passenger')
            ->findOneById($this->getParam('id'));

        if (null === $passenger) {
            $this->flashMessenger()->error(
                'Error',
                'No passenger with the given ID was found!'
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
