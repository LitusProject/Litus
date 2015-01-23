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

use DateTime,
    PromBundle\Entity\Bus,
    PromBundle\Entity\Bus\Passenger,
    PromBundle\Entity\Bus\ReservationCode,
    Zend\Mail\Message,
    Zend\View\Model\ViewModel;

/**
 * BusController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class BusController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('PromBundle\Entity\Bus')
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

    public function addAction()
    {
        $form = $this->getForm('prom_bus_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $departureTime = self::_loadDate($formData['departure_time']);

                $newBus = new Bus($departureTime, $formData['nb_passengers']);

                $this->getEntityManager()->persist($newBus);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The bus was successfully added!'
                );

                $this->redirect()->toRoute(
                    'prom_admin_bus',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        if (!($bus = $this->_getBus())) {
            return new ViewModel();
        }

        $mail = new Message();

        foreach ($bus->getReservedSeatsArray() as $passenger) {
            $passenger->setBus(null);
            $mail->addBcc($passenger->getEmail());
        }

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('prom.remove_mail')
        );

        $mail->setBody(str_replace('{{ busTime }}', $bus->getDepartureTime()->format('d/m/Y h:i'), $mailData['body']))
            ->setFrom($mailData['from'])
            ->addBcc($mailData['from'])
            ->setSubject($mailData['subject']);

        if ('development' != getenv('APPLICATION_ENV')) {
            $this->getMailTransport()->send($mail);
        }

        $this->getEntityManager()->remove($bus);
        $this->getEntityManager()->flush();

        $this->redirect()->toRoute(
            'prom_admin_bus',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }

    public function viewAction()
    {
        if (!($bus = $this->_getBus())) {
            return new ViewModel();
        }

        $passengers = $bus->getReservedSeatsArray();

        return new ViewModel(
            array(
                'passengers' => $passengers,
            )
        );
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }

    private function _getBus()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the bus!'
            );

            $this->redirect()->toRoute(
                'prom_admin_bus',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $bus = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->findOneById($this->getParam('id'));

        if (null === $bus) {
            $this->flashMessenger()->error(
                'Error',
                'No bus with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'prom_admin_bus',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $bus;
    }
}
