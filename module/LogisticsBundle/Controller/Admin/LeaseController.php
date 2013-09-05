<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
namespace LogisticsBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Controller\ActionController\AdminController,
    LogisticsBundle\Entity\Lease\Item,
    LogisticsBundle\Form\Admin\Lease\Add as AddItemForm,
    LogisticsBundle\Form\Admin\Lease\Edit as EditItemForm,
    Zend\View\Model\ViewModel;

/**
 * LeaseController
 *
 * Controller for /admin/logistics/lease[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class LeaseController extends AdminController
{
    public function manageAction()
    {
        $items = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Item')
                ->findAll();

        $paginator = $this->paginator()->createFromArray(
                $items, $this->getParam('page')
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
        $form = new AddItemForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $item = new Item($formData['name'], $formData['barcode'], $formData['additional_info']);
                $this->getEntityManager()->persist($item);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The item was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'logistics_admin_lease',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel;
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
        if (!($item = $this->_getItem()))
            return new ViewModel;

        $form  = new EditItemForm($this->getEntityManager(), $item);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $item->setName($formData['name']);
                $item->setBarcode($formData['barcode']);
                $item->setAdditionalInfo($formData['additional_info']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The item was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'logistics_admin_lease',
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

    public function deleteAction()
    {
        $this->initAjax();

        if (!($item = $this->_getItem()))
            return new ViewModel;


        $leaseRepo = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Lease');
        /* @var $leaseRepo \LogisticsBundle\Repository\Lease\Lease */
        if(count($leaseRepo->findUnreturnedByItem($item)) > 0) {

            return new ViewModel(
                array(
                    'result' => array(
                        'status' => 'unreturned_leases'
                    ),
                )
            );
        }

        $leases = $leaseRepo->findByItem($item);
        foreach($leases as $lease) {
            $this->getEntityManager()->remove($lease);
        }

        $this->getEntityManager()->remove($item);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    /**
     * @return null|\LogisticsBundle\Entity\Lease\Item
     */
    private function _getItem()
    {
        if ($this->getParam('id') === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the item!'
                )
            );

            $this->redirect()->toRoute(
                'logistics_admin_lease',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $item = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Lease\Item')
            ->findOneById($this->getParam('id'));

        if ($item === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No item with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'logistics_admin_lease',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $item;
    }
}
