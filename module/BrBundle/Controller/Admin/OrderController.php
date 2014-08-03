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

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Collaborator,
    BrBundle\Entity\Contract,
    BrBundle\Entity\Contract\ContractEntry,
    BrBundle\Entity\Contract\ContractHistory,
    BrBundle\Entity\Product\Order,
    BrBundle\Entity\Product\OrderEntry,
    BrBundle\Form\Admin\Order\Add as AddForm,
    BrBundle\Form\Admin\Order\Edit as EditForm,
    BrBundle\Form\Admin\Order\AddProduct as AddProductForm,
    Zend\View\Model\ViewModel;

/**
 * OrderController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class OrderController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Product\Order',
            $this->getParam('page'),
            array(
                'old' => false,
            )
        );

        foreach($paginator as $order)
            $order->setEntityManager($this->getEntityManager());

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager(), $this->getCurrentAcademicYear());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $contact = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\User\Person\Corporate')
                    ->findOneById($formData['contact']);

                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']);

                $tax = ($formData['tax'] == true);

                $collaborator = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Collaborator')
                    ->findCollaboratorByPersonId($this->getAuthentication()->getPersonObject()->getId());

                $order = new Order(
                    $contact,
                    $collaborator,
                    $tax
                );

                $contract = new Contract($order,
                    $collaborator,
                    $company,
                    $formData['discount'],
                    $formData['title']
                );

                $contract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextContractNb()
                );

                if(!($formData['discount_context'] == ''))
                    $contract->setDiscountContext($formData['discount_context']);

                $this->getEntityManager()->persist($order);
                $this->getEntityManager()->persist($contract);

                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'br_admin_order',
                    array(
                        'action' => 'product',
                        'id' => $order->getId(),
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

    public function productAction()
    {
        if (!($order = $this->_getOrder(false)))
            return new ViewModel();

        if ($order->getContract()->isSigned() == true)
            return new ViewModel();

        $entries = $order->getEntries();

        $oldContract = $order->getContract();

        $currentProducts = array();
        foreach ($entries as $entry)
            array_push($currentProducts, $entry->getProduct());

        $form = new AddProductForm($currentProducts, $this->getEntityManager(), $this->getCurrentAcademicYear());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $collaborator = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Collaborator')
                    ->findCollaboratorByPersonId($this->getAuthentication()->getPersonObject()->getId());

                $updatedOrder = new Order(
                    $order->getContact(),
                    $collaborator,
                    $order->isTaxFree()
                );

                $contract = new Contract($updatedOrder,
                    $collaborator,
                    $oldContract->getCompany(),
                    $oldContract->getDiscount(),
                    $oldContract->getTitle()
                );

                $contract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextContractNb()
                );

                $contract->setDiscountContext($order->getContract()->getDiscountContext());

                $counter = 0;

                foreach ($entries as $entry) {
                    $contractEntry = new ContractEntry($contract, $orderEntry, $counter,0);
                    $orderEntry = new OrderEntry($updatedOrder, $entry->getProduct(), $entry->getQuantity());
                    $order->setEntry($orderEntry);
                    $contract->setEntry($contractEntry);
                    $counter++;
                    $this->getEntityManager()->persist($orderEntry);
                    $this->getEntityManager()->persist($contractEntry);
                }

                $newProduct = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Product')
                    ->findProductById($formData['product']);
                $orderEntry = new OrderEntry($updatedOrder, $newProduct[0], $formData['amount']);
                $contractEntry = new ContractEntry($contract, $orderEntry, $counter,0);
                $order->setEntry($orderEntry);
                $contract->setEntry($contractEntry);

                $this->getEntityManager()->persist($orderEntry);
                $this->getEntityManager()->persist($contractEntry);
                $this->getEntityManager()->persist($updatedOrder);
                $this->getEntityManager()->persist($contract);

                $history = new ContractHistory($contract);
                $this->getEntityManager()->persist($history);

                $order->setOld();

                $this->getEntityManager()->persist($order);

                $this->getEntityManager()->flush();
                $this->redirect()->toRoute(
                    'br_admin_order',
                    array(
                        'action' => 'product',
                        'id' => $updatedOrder->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'order' => $order,
                'entries' => $entries,
                'addProductForm' => $form,
            )
        );
   }

    public function editAction()
    {
        if (!($order = $this->_getOrder(false)))
            return new ViewModel();

        if ($order->getContract()->isSigned() == true)
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $this->getCurrentAcademicYear(), $order);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $collaborator = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Collaborator')
                    ->findCollaboratorByPersonId($this->getAuthentication()->getPersonObject()->getId());

                $contact = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\User\Person\Corporate')
                    ->findOneById($formData['contact']);

                $tax = ($formData['tax'] == true);

                $updatedOrder = new Order(
                    $contact,
                    $collaborator,
                    $tax
                );

                $updatedContract = new Contract($updatedOrder,
                    $collaborator,
                    $order->getCompany(),
                    $formData['discount'],
                    $formData['title']
                );

                $updatedContract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextContractNb()
                );

                $entries = $order->getEntries();

                $counter = 0;
                foreach ($entries as $entry) {
                    $updatedOrderEntry = new OrderEntry($updatedOrder, $entry->getProduct(), $entry->getQuantity());
                    $updatedContractEntry = new ContractEntry($updatedContract, $updatedOrderEntry,$counter, 0);
                    $counter++;
                    $this->getEntityManager()->persist($updatedOrderEntry);
                    $this->getEntityManager()->persist($updatedContractEntry);

                }

                $this->getEntityManager()->persist($updatedOrder);
                $this->getEntityManager()->persist($updatedContract);

                $order->setOld();

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The order was succesfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_order',
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

        if (!($order = $this->_getOrder()))
            return new ViewModel();

        $this->getEntityManager()->remove($order);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteProductAction()
    {
        $this->initAjax();

        if (!($entry = $this->_getEntry(false)))
             return new ViewModel();

        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Product\Order',
            $this->getParam('page'),
            array(
                'old' => true,
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    private function _getOrder($allowSigned = true)
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the order!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $order = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product\Order')
            ->findOneById($this->getParam('id'));

        if (null === $order) {
            $this->flashMessenger()->error(
                'Error',
                'No order with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }
        if ($order->getContract()->isSigned() && !$allowSigned) {
            $this->flashMessenger()->error(
                'Error',
                'The given order\'s contract has been signed! Signed orders cannot be modified.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $order;
    }

    private function _getEntry($allowSigned = true)
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the order entry!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $entry = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product\OrderEntry')
            ->findOneById($this->getParam('id'));

        if (null === $entry) {
            $this->flashMessenger()->error(
                'Error',
                'No order entry with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }
        if ($entry->getOrder()->getContract()->isSigned() && !$allowSigned) {
            $this->flashMessenger()->error(
                'Error',
                'The given order\'s contract has been signed! Signed orders cannot be modified.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $entry;
    }
}
