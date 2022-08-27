<?php

namespace LogisticsBundle\Controller\Admin;

use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

class InventoryController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Inventory')
                ->findAllNotZeroQuery(),
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
        $form = $this->getForm('logistics_inventory_add');

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
                    $article = $form->hydrateObject();
                    $this->getEntityManager()->persist($article);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The article was succesfully created!'
                    );

                    $this->redirect()->toRoute(
                        'logistics_admin_inventory',
                        array(
                            'action' => 'add',
                        )
                    );

                    return new ViewModel();
                } else {
                    $amount = $formData['amount'];
                    $article->addAmount($amount);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The article was succesfully created!'
                    );

                    $this->redirect()->toRoute(
                        'logistics_admin_inventory',
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