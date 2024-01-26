<?php

namespace LogisticsBundle\Controller;

use Laminas\View\Model\ViewModel;

class InventoryController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Inventory')
                ->findAllNotZeroQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('logistics_inventory_inventory');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $article = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Inventory')
                    ->findOneBy(
                        array('barcode' => $formData['barcode'])
                    );

                if ($article === null) {
                    $amount = $formData['amount'];
                    if ($amount <= 0) {
                        $this->flashMessenger()->error(
                            'Error',
                            'Please give an amount other than zero!'
                        );
                        $this->redirect()->toRoute(
                            'logistics_inventory',
                            array(
                                'action' => 'add',
                            )
                        );
                        return new ViewModel();
                    }
                    $this->getEntityManager()->persist(
                        $form->hydrateObject()
                    );
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The article was succesfully created!'
                    );

                    $this->redirect()->toRoute(
                        'logistics_inventory',
                        array(
                            'action' => 'add',
                        )
                    );

                    return new ViewModel();
                } else {
                    $amount = $formData['amount'];

                    $expiry_date = $formData['expiry_date'];

                    if ($amount > 0) {
                        $article->addAmount($amount);
                        if (strlen($expiry_date) > 0) {
                            $article->setExpiryDate($expiry_date);
                        }
                    } elseif ($amount < 0) {
                        $new_reserved = $article->getReserved() + $amount;
                        if ($new_reserved < 0) {
                            $this->flashMessenger()->error(
                                'Error',
                                'Not enough articles reserved!'
                            );
                            $this->redirect()->toRoute(
                                'logistics_inventory',
                                array(
                                    'action' => 'add',
                                )
                            );
                            return new ViewModel();
                        }
                        if (strlen($expiry_date) > 0) {
                            $article->setExpiryDate($expiry_date);
                        }
                        $article->subtractReserved($amount);
                    } else {
                        $this->flashMessenger()->error(
                            'Error',
                            'Please give an amount other than zero!'
                        );
                        $this->redirect()->toRoute(
                            'logistics_inventory',
                            array(
                                'action' => 'add',
                            )
                        );
                        return new ViewModel();
                    }

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The article was succesfully created!'
                    );

                    $this->redirect()->toRoute(
                        'logistics_inventory',
                        array(
                            'action' => 'add',
                        )
                    );

                    return new ViewModel();
                }
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
        $inventory = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Inventory')
            ->findOneById($this->getParam('id'));
        $form = $this->getForm('logistics_inventory_edit', array('inventory' => $inventory));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->getEntityManager()->flush(); // Sends cache to database

                $this->redirect()->toRoute(
                    'logistics_inventory'
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function reserveAction()
    {
        $inventory = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Inventory')
            ->findOneById($this->getParam('id'));
        $form = $this->getForm('logistics_inventory_reserve');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $reserved = $formData['reserve'];

                // If the available amount (amount - already reserved) subtracted with the newly reserved items is
                // smaller than zero, there has to be thrown an error
                $new_amount = $inventory->getAmount() - $inventory->getReserved() - $reserved;

                // When reserved items will be put back in inventory, reserved will be < 0
                // If the final reserved amount < 0, there will be thrown an error
                $new_reserved = $inventory->getReserved() + $reserved;

                if ($new_amount < 0) {
                    $this->flashMessenger()->error(
                        'Error',
                        'Not enough articles available!'
                    );
                    $this->redirect()->toRoute(
                        'logistics_inventory',
                        array(
                            'action' => 'reserve',
                            'id'     => $inventory->getId(),
                        )
                    );
                } elseif ($new_reserved < 0) {
                    $this->flashMessenger()->error(
                        'Error',
                        'Not enough articles reserved to return!'
                    );
                    $this->redirect()->toRoute(
                        'logistics_inventory',
                        array(
                            'action' => 'reserve',
                            'id'     => $inventory->getId(),
                        )
                    );
                } else {
                    // Used to add or subtract reserved form available
                    $inventory->addReserved($reserved);
                    $this->getEntityManager()->flush();

                    $this->redirect()->toRoute(
                        'logistics_inventory'
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'form'      => $form,
                'inventory' => $inventory,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();
    }
}
