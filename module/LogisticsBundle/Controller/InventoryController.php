<?php

namespace LogisticsBundle\Controller;

use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Inventory;

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
                    $article = $form->hydrateObject();
                    $this->getEntityManager()->persist($article);
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

                    if ($amount > 0) {
                        $article->addAmount($amount);
                        $article->setExpiryDate($formData['expiry_date']);
                    } elseif ($amount < 0) {
                        $new_amount = $article->getAmount() + $amount;
                        if ($new_amount < 0) {
                            $this->flashMessenger()->error(
                                'Error',
                                'Not enough articles left!'
                            );
                            $this->redirect()->toRoute(
                                'logistics_inventory',
                                array(
                                    'action' => 'add',
                                )
                            );
                            return new ViewModel();
                        }
                        $article->setExpiryDate($formData['expiry_date']);
                        $article->subtractAmount($amount);
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
}