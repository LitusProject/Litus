<?php

namespace LogisticsBundle\Controller;

use LogisticsBundle\Component\Controller\LogisticsController,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    LogisticsBundle\Entity\Lease\Item,
    LogisticsBundle\Entity\Lease\Lease,
    LogisticsBundle\Form\Lease\AddLease as AddLeaseForm,
    LogisticsBundle\Form\Lease\AddReturn as AddReturnForm,
    Zend\View\Model\ViewModel,
    DateTime;

/**
 * Controller for /logistics/lease[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class LeaseController extends LogisticsController
{
    public function indexAction()
    {
        $leases = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                ->findAllUnreturned();

        return new ViewModel(
            array(
                'leases'=>$leases,
            )
        );
    }

    public function leaseAction()
    {
        $form = new AddLeaseForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $data = $form->getFormData($formData);
                $item = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Item')
                        ->findOneByBarcode($data['barcode']);
                $lease = new Lease(
                    $item,
                    new DateTime,
                    $this->getAuthentication()->getPersonObject(),
                    $data['leased_to'],
                    $data['leased_pawn']
                );
                $this->getEntityManager()->persist($lease);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The lease was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'logistics_lease',
                    array(
                        'action'=> 'show',
                        'id'=>$lease->getId(),
                    )
                );

                return new ViewModel;
            }
        }

        return new ViewModel(
            array(
                'form'=> $form,
            )
        );
    }

    public function returnAction() {
        $form = new AddReturnForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $data = $form->getFormData($formData);

                $item = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Item')
                        ->findOneByBarcode($data['barcode']);
                /* @var $item \LogisticsBundle\Entity\Lease\Item */
                $lease = current($this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                        ->findUnreturnedByItem($item));
                /* @var $lease \LogisticsBundle\Entity\Lease\Lease */
                $lease->setReturned(true);
                $lease->setReturnedTo($this->getAuthentication()->getPersonObject());
                $lease->setReturnedDate(new DateTime);
                $lease->setReturnedPawn($data['returned_pawn']);
                $lease->setReturnedBy($data['returned_by']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The return was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'logistics_lease',
                    array(
                        'action'=> 'show',
                        'id'=>$lease->getId(),
                    )
                );

                return new ViewModel;
            }
        }

        return new ViewModel(
            array(
                'form'=> $form,
            )
        );
    }

    public function showAction()
    {
        if(!($lease = $this->_getLease()))
            return new ViewModel;

        return new ViewModel(
            array(
                'lease'=>$lease,
            )
        );
    }

    public function searchAction()
    {
        $query = $this->getRequest()->getQuery('q');
        $results = array();
        if($query !== null) {
            $items = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Lease\Item')
                    ->searchByName($query);
            foreach($items as $item) {
                /* @var $item \LogisticsBundle\Entity\Lease\Item */
                $results[] = array(
                    'id' => $item->getBarcode(),
                    'value' => $item->getName(),
                    'additional_info' => $item->getAdditionalInfo(),
                );
            }
        }

        return new ViewModel(
            array(
                'result'=>$results,
            )
        );
    }

    /**
     *
     * @return null|\LogisticsBundle\Entity\Lease\Lease
     */
    private function _getLease()
    {
        if ($this->getParam('id') === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the lease!'
                )
            );

            $this->redirect()->toRoute('logistics_lease');

            return;
        }

        $lease = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                ->find($this->getParam('id'));

        if ($lease === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No lease with the given id was found!'
                )
            );

            $this->redirect()->toRoute('logistics_lease');

            return;
        }

        return $lease;
    }
}
