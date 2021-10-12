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
                if($entity === null) {
                    return new ViewModel(
                        array(
                            'noEntity' => 'No consumptions were found',
                            'form' => $this->getForm('ticket_sale_consume'),
                        )
                    );
                }

                if ($entity->getConsumptions() - $amount < 0) {

                    return new ViewModel(
                        array(
                            'amount' => $entity->getConsumptions(),
                            'msg' => 'error',
                            'name' => $entity->getFullName(),
                            'form' => $form,
                        )
                    );
                }
                if ($entity->getConsumptions() - $amount === 0) {
                    $this->getEntityManager()->remove($entity);
                    $this->getEntityManager()->flush();

                    return new ViewModel(
                        array(
                            'empty' => "All consumptions used",
                            'name' => $entity->getFullName(),
                            'form' => $this->getForm('ticket_sale_consume'),
                        )
                    );
                }
                $entity->removeConsumptions($amount);
                $this->getEntityManager()->flush();

                return new ViewModel(
                    array(
                        'amount_left' => $entity->getConsumptions(),
                        'name' => $entity->getFullName(),
                        'form' => $this->getForm('ticket_sale_consume'),
                    )
                );
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