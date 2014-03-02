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
 * LeaseController
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class LeaseController extends LogisticsController
{
    public function indexAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                ->findAllUnreturnedQuery(),
            $this->getParam('page')
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

    public function showAction()
    {
        if(!($lease = $this->_getLease()))

            return new ViewModel();

        return new ViewModel(
            array(
                'lease'=>$lease,
            )
        );
    }

    public function historyAction()
    {
        if(!($item = $this->_getItem($this->getRequest()->getQuery('barcode'))))

            return new ViewModel();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                ->findByItemQuery($item),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'item'=>$item,
                'leases'=>$paginator,
                'paginationControl'=>$this->paginator()->createControl()
            )
        );
    }

    public function typeaheadAction()
    {
        $query = $this->getRequest()->getQuery('q');
        $purpose = $this->getRequest()->getQuery('purpose');
        $results = array();
        if ($query !== null) {
            $items = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Item')
                ->findAllByName($query);
            $leaseRepo = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Lease');

            foreach ($items as $item) {
                if($purpose === 'lease' && count($leaseRepo->findUnreturnedByItem($item)) > 0)
                    continue;
                if($purpose === 'return' && count($leaseRepo->findUnreturnedByItem($item)) <= 0)
                    continue;

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

        if ($item) {
            $leases = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                ->findUnreturnedByItem($item);
            if (count($leases) > 0) {
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
                    'status' => $status,
                    'additional_info' => $item->getAdditionalInfo(),
                )
            )
        );
    }

    private function _handleLeaseForm()
    {
        $form = new AddLeaseForm($this->getEntityManager(), 'lease');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (isset($formData['lease'])) {
                $form->setData($formData);

                if ($form->isValid()) {
                    $formData = $form->getFormData($formData);

                    $item = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Item')
                        ->findOneByBarcode($formData['barcode']);

                    $lease = new Lease(
                        $item,
                        $formData['leased_amount'],
                        new DateTime,
                        $this->getAuthentication()->getPersonObject(),
                        $formData['leased_to'],
                        $formData['leased_pawn'],
                        $formData['comment']
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

    private function _handleReturnForm()
    {
        $form = new AddReturnForm($this->getEntityManager(), 'return');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (isset($formData['return'])) {
                $form->setData($formData);

                if ($form->isValid()) {
                    $data = $form->getFormData($formData);

                    $item = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Item')
                        ->findOneByBarcode($data['barcode']);

                    $lease = current($this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                        ->findUnreturnedByItem($item));

                    $lease->setReturned(true)
                        ->setReturnedAmount($data['returned_amount'])
                        ->setReturnedTo($this->getAuthentication()->getPersonObject())
                        ->setReturnedDate(new DateTime)
                        ->setReturnedPawn($data['returned_pawn'])
                        ->setReturnedBy($data['returned_by'])
                        ->setReturnedComment($data['comment']);

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

    private function _getItem($barcode = null)
    {
        if ($this->getParam('id') === null && $barcode === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id or barcode was given to identify the item!'
                )
            );

            $this->redirect()->toRoute('logistics_lease');

            return;
        }

        if ($barcode) {
            $item = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Item')
                ->findOneByBarcode($barcode);
        } else {
            $item = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Item')
                ->find($this->getParam('id'));
        }

        if ($item === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No item with the given id or barcode was found!'
                )
            );

            $this->redirect()->toRoute('logistics_lease');

            return;
        }

        return $item;
    }
}
