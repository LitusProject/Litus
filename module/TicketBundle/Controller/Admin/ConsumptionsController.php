<?php

namespace TicketBundle\Controller\Admin;

use CommonBundle\Component\Form\Admin\Element\DateTime;
use Laminas\View\Model\ViewModel;
use TicketBundle\Entity\Consumptions;
use TicketBundle\Entity\Transactions;

/**
 * ConsumptionsController
 */
class ConsumptionsController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Consumptions')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function consumeAction()
    {
        $form = $this->getForm('ticket_consumptions_consume');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $data = $form->getData()['consume'];

                $entity = $this->getConsumptionsEntity();

                if ($entity instanceof Consumptions) {
                    $person = $this->getPersonEntity();

                    $transaction = new Transactions(-$data, $entity->getPerson(), $person);
                    $this->getEntityManager()->persist($transaction);
                }

                if ($entity->getConsumptions() - $data < 0) {
                    $this->flashMessenger()->error(
                        'Error',
                        'Not enough consumptions left!'
                    );

                    $this->redirect()->toRoute(
                        'ticket_admin_consumptions',
                        array(
                            'action' => 'manage',
                        )
                    );
                    return new ViewModel();
                }

                if ($entity->getConsumptions() - $data === 0) {
                    $entity->removeConsumptions($data);

                    $this->getEntityManager()->remove($entity);
                    $this->getEntityManager()->flush();

                    $this->redirect()->toRoute(
                        'ticket_admin_consumptions',
                        array(
                            'action' => 'manage',
                        )
                    );
                    return new ViewModel();
                }

                $entity->removeConsumptions($data);

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'ticket_admin_consumptions',
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

    public function addAction()
    {
        $form = $this->getForm('ticket_consumptions_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $consumption = $form->hydrateObject();
                $this->getEntityManager()->persist(
                    $consumption
                );
//                $this->getEntityManager()->persist($form);

                if ($consumption instanceof Consumptions){
                    $person = $this->getPersonEntity();

                    $transaction = new Transactions($form->getData()['number_of_consumptions'], $consumption->getPerson(), $person);
                    $this->getEntityManager()->persist($transaction);
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The consumptions were succesfully created!'
                );

                $this->redirect()->toRoute(
                    'ticket_admin_consumptions',
                    array(
                        'action' => 'add',
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

    public function editAction()
    {
        $consumptions = $this->getConsumptionsEntity();
        if ($consumptions === null) {
            return new ViewModel();
        }
        $old = $consumptions->getConsumptions();
        $form = $this->getForm('ticket_consumptions_edit', array('consumptions' => $consumptions));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                if ($consumptions instanceof Consumptions){
                    $person = $this->getPersonEntity();
                    $transaction = new Transactions($form->getData()['number_of_consumptions'] - $old, $consumptions->getPerson(), $person);
                    $this->getEntityManager()->persist($transaction);
                }
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The consumptions were succesfully updated!'
                );

                $this->redirect()->toRoute(
                    'ticket_admin_consumptions',
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
        $this->initAjax();

        $consumptions = $this->getConsumptionsEntity();

        if ($consumptions === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($consumptions);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'succes'),
            )
        );
    }

    public function transactionsAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Transactions')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    private function getConsumptionsEntity()
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

    public function searchAction()
    {
        $this->initAjax();
        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $consumptions = $this->search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($consumptions as $consumption) {
            $item = (object) array();
            $item->id = $consumption->getId();
            $item->name = $consumption->getFullName();
            $item->username = $consumption->getUserName();
            $item->consumptions = $consumption->getConsumptions();

            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function search()
    {
        switch ($this->getParam('field')) {
            case 'username':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Consumptions')
                    ->findAllByUserNameQuery($this->getParam('string'));
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Consumptions')
                    ->findAllByNameQuery($this->getParam('string'));
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
