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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Collaborator;
use BrBundle\Entity\Contract;
use BrBundle\Entity\Contract\Entry as ContractEntry;
use BrBundle\Entity\Contract\History;
use BrBundle\Entity\Event\CompanyMap;
use BrBundle\Entity\Product\Order;
use BrBundle\Entity\Product\Order\Entry as OrderEntry;
use Laminas\View\Model\ViewModel;

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
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Product\Order')
                ->findAllNotOldUnsignedQuery(),
            $this->getParam('page')
        );

        foreach ($paginator as $order) {
            $order->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function signedAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Product\Order')
                ->findAllNotOldSignedQuery(),
            $this->getParam('page')
        );

        foreach ($paginator as $order) {
            $order->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $collaborator = $this->getCollaboratorEntity();
        if ($collaborator === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_order_add', array('current_year' => $this->getCurrentAcademicYear()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $order = $form->hydrateObject(
                    new Order($collaborator)
                );

                $this->getEntityManager()->persist($order);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute(
                    'br_admin_order',
                    array(
                        'action' => 'product',
                        'id'     => $order->getId(),
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
        $order = $this->getOrderEntity(false);
        if ($order === null) {
            return new ViewModel();
        }

        if ($order->hasContract() && $order->getContract()->isSigned()) {
            return new ViewModel();
        }

        if ($this->getCollaboratorEntity() === null) {
            return new ViewModel();
        }

        $entries = $order->getEntries();

        $currentProducts = array();
        foreach ($entries as $entry) {
            $currentProducts[] = $entry->getProduct();
        }

        $form = $this->getForm('br_order_add-product', array('order' => $order, 'current_products' => $currentProducts, 'current_year' => $this->getCurrentAcademicYear()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                if ($order->hasContract()) {
                    $this->redirect()->toRoute(
                        'br_admin_order',
                        array(
                            'action' => 'manage',
                        )
                    );
                } else {
                    $this->getEntityManager()->flush();
                    $this->redirect()->toRoute(
                        'br_admin_order',
                        array(
                            'action' => 'product',
                            'id'     => $order->getId(),
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'order'          => $order,
                'entries'        => $entries,
                'addProductForm' => $form,
            )
        );
    }

    public function editAction()
    {
        $order = $this->getOrderEntity(false);
        if ($order === null) {
            return new ViewModel();
        }

        if ($order->hasContract() && $order->getContract()->isSigned()) {
            return new ViewModel();
        }

        if ($this->getCollaboratorEntity() === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_order_edit', array('order' => $order, 'current_year' => $this->getCurrentAcademicYear()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
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

    public function editProductAction()
    {
        $order = $this->getOrderEntity(false);
        if ($order === null) {
            return new ViewModel();
        }

        if ($order->hasContract() && $order->getContract()->isSigned()) {
            return new ViewModel();
        }

        if ($this->getCollaboratorEntity() === null) {
            return new ViewModel();
        }

        $entry = $this->getEntityById('BrBundle\Entity\Product\Order\Entry', 'entry');

        $currentProducts = array();

        $form = $this->getForm('br_order_edit-product', array('order' => $order, 'entry' => $entry, 'current_products' => $currentProducts, 'current_year' => $this->getCurrentAcademicYear()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                if ($order->hasContract()) {
                    $this->redirect()->toRoute(
                        'br_admin_order',
                        array(
                            'action' => 'manage',
                        )
                    );
                } else {
                    $this->redirect()->toRoute(
                        'br_admin_order',
                        array(
                            'action' => 'product',
                            'id'     => $order->getId(),
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'order'           => $order,
                'entry'           => $entry,
                'editProductForm' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $order = $this->getOrderEntity();
        if ($order === null) {
            return new ViewModel();
        }

        $order->setOld();

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

        $entry = $this->getEntryEntity(false);
        if ($entry === null) {
            return new ViewModel();
        }

        if ($entry->getOrder()->hasContract()) {
            $contract_entries = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract\Entry')
                ->findAllContractEntriesByOrderEntry($entry);

            foreach ($contract_entries as $c_entry) {
                $this->getEntityManager()->remove($c_entry);
            }
        }

        if ($entry->getOrder()->getAutoDiscountPercentage() > 0) {
            $order = $entry->getOrder();
        }

        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();

        if (isset($order)) {
            $order->setEntityManager($this->getEntityManager())->setAutoDiscountPercentage();
        }
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
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function generateAction()
    {
        $order = $this->getOrderEntity(false);
        if ($order === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_order_generate-contract', array('order' => $order, 'language' => $this));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $contract = $form->hydrateObject(
                    new Contract(
                        $order,
                        $order->getCreationPerson(),
                        $order->getCompany(),
                        $formData['title']
                    )
                );

                $contract->setContractNb(
                    $this->getEntityManager()
                        ->getRepository('BrBundle\Entity\Contract')
                        ->findNextContractNbByCollaborator($contract->getAuthor())
                );

                $order->setContract($contract);

                $entries = $order->getEntries();

                $counter = 0;
                foreach ($entries as $entry) {
                    $contract->setEntry(
                        new ContractEntry(
                            $contract,
                            $entry,
                            $counter,
                            0
                        )
                    );
                    if ($entry->getProduct()->getEvent() !== null) {
                        $eventCompanyMaps = $this->getEntityManager()
                            ->getRepository('BrBundle\Entity\Event\CompanyMap')
                            ->findAllByEvent($entry->getProduct()->getEvent());

                        if (in_array($order->getCompany(), $eventCompanyMaps) === false) {
                            $map = new CompanyMap($order->getCompany(), $entry->getProduct()->getEvent());
                            array_push($eventCompanyMaps, $map);
                            $this->getEntityManager()->persist($map);
                        }
                    }

                    $counter++;
                }
                $this->getEntityManager()->persist($contract);
                $this->getEntityManager()->persist(new History($contract));

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The contract was succesfully generated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_contract',
                    array(
                        'action' => 'manage',
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

    /**
     * @param  boolean $allowSigned
     * @return Order|null
     */
    private function getOrderEntity($allowSigned = true)
    {
        $order = $this->getEntityById('BrBundle\Entity\Product\Order');

        if (!($order instanceof Order)) {
            $this->flashMessenger()->error(
                'Error',
                'No order was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        if ($order->hasContract() && $order->getContract()->isSigned() && !$allowSigned) {
            $this->flashMessenger()->error(
                'Error',
                'The given order\'s contract has been signed! Signed orders cannot be modified.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $order;
    }

    /**
     * @param  boolean $allowSigned
     * @return OrderEntry|null
     */
    private function getEntryEntity($allowSigned = true)
    {
        $entry = $this->getEntityById('BrBundle\Entity\Product\Order\Entry');

        if (!($entry instanceof OrderEntry)) {
            $this->flashMessenger()->error(
                'Error',
                'No entry was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        if ($entry->getOrder()->hasContract() && $entry->getOrder()->getContract()->isSigned() && !$allowSigned) {
            $this->flashMessenger()->error(
                'Error',
                'The given order\'s contract has been signed! Signed orders cannot be modified.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $entry;
    }

    /**
     * @return Collaborator|null
     */
    private function getCollaboratorEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->flashMessenger()->error(
                'Error',
                'You are not a collaborator, so you cannot add or edit orders.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $collaborator = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Collaborator')
            ->findCollaboratorByPersonId($this->getAuthentication()->getPersonObject()->getId());

        if ($collaborator === null) {
            $this->flashMessenger()->error(
                'Error',
                'You are not a collaborator, so you cannot add or edit orders.'
            );

            $this->redirect()->toRoute(
                'br_admin_order',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $collaborator;
    }
}
