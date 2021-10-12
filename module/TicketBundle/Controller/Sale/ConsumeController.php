<?php

namespace TicketBundle\Controller\Sale;

use Laminas\View\Model\ViewModel;
use TicketBundle\Entity\Consumptions as Consumptions;


class ConsumeController extends \TicketBundle\Component\Controller\SaleController{
    public function consumeAction() {
        $form = $this->getForm('ticket_sale_consume');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $amount = $form->getData()['amount'];
                $username = $form->getData()['username'];
                $entity = $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Consumptions')
                    ->findAllByUserNameQuery($username)->getResult()[0];
                if ($entity->getConsumptions() - $amount < 0) {
//                    $this->flashMessenger()->error(
//                        'Error',
//                        'Not enough consumptions left!'
//                    );
                    return new ViewModel(
                        array(
                            'message' => "test",
                            'form' => $form,
                        )
                    );
                }
                $entity->removeConsumptions($amount);
                $this->getEntityManager()->flush();

            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function getConsumptionsEntity()
    {
        $consumptions = $this->getEntityById('TicketBundle\Entity\Consumptions');

        if (!($consumptions instanceof Consumptions)) {
            $this->flashMessenger()->error(
                'Error',
                'No consumptions were found!'
            );

            $this->redirect()->toRoute(
                'ticket_admin_consumptions',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $consumptions;
    }
}