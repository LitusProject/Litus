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

        $paginator = $this->paginator()->createFromArray(
                $leases, $this->getParam('page')
        );

        $leaseForm = $this->_handleLeaseForm();
        $returnForm = $this->_handleReturnForm();

        return new ViewModel(
            array(
                'leases'=> $paginator,
                'paginationControl'=>  $this->paginator()->createControl(),
                'leaseForm' => $leaseForm,
                'returnForm' => $returnForm,
            )
        );
    }

    /**
     * Handles submissions and creation of the lease form
     * @return \LogisticsBundle\Form\Lease\AddLease
     */
    private function _handleLeaseForm()
    {
        $form = new AddLeaseForm($this->getEntityManager(), 'lease');

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if(isset($formData['lease'])) {
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

                }
            }
        }

        return $form;
    }

    /**
     * Handles submissions and creation of the return form
     * @return \LogisticsBundle\Form\Lease\AddReturn
     */
    private function _handleReturnForm()
    {
        $form = new AddReturnForm($this->getEntityManager(), 'return');

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if(isset($formData['return'])) {
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
                }
            }
        }

        return $form;
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

    public function typeaheadAction()
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

    public function availabilityCheckAction()
    {
        $barcode = $this->getParam('id');
        $item = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Item')
                ->findOneByBarcode($barcode);
        if($item) {
            $leases = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                    ->findUnreturnedByItem($item);
            if(count($leases) > 0) {
                $status = 'leased';
            } else {
                $status = 'returned';
            }
        } else {
            $status = 'noSuchItem';
        }

        return new ViewModel(
            array(
                'result' => array(
                    'status' => $status
                )
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
