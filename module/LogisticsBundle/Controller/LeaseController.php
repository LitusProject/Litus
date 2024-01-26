<?php

namespace LogisticsBundle\Controller;

use DateTime;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Lease;
use LogisticsBundle\Entity\Lease\Item;

/**
 * LeaseController
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class LeaseController extends \LogisticsBundle\Component\Controller\LogisticsController
{
    public function indexAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease')
                ->findAllUnreturnedQuery(),
            $this->getParam('page')
        );

        $leaseForm = $this->handleLeaseForm();
        $returnForm = $this->handleReturnForm();

        return new ViewModel(
            array(
                'leases'            => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'leaseForm'         => $leaseForm,
                'returnForm'        => $returnForm,
            )
        );
    }

    public function showAction()
    {
        $lease = $this->getLeaseEntity();
        if ($lease === null) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'lease' => $lease,
            )
        );
    }

    public function historyAction()
    {
        $item = $this->getItemEntity($this->getRequest()->getQuery('searchItem')['id']);
        if ($item === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease')
                ->findByItemQuery($item),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'item'              => $item,
                'leases'            => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $query = $this->getRequest()->getQuery('q');
        $purpose = $this->getRequest()->getQuery('purpose');

        $results = array();
        if ($query !== null) {
            $items = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Item')
                ->findAllByNameOrBarcode($query);
            $leaseRepo = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease');

            foreach ($items as $item) {
                if ($purpose === 'lease' && count($leaseRepo->findUnreturnedByItem($item)) > 0) {
                    continue;
                }
                if ($purpose === 'return' && count($leaseRepo->findUnreturnedByItem($item)) <= 0) {
                    continue;
                }

                $results[] = array(
                    'id'              => $item->getId(),
                    'value'           => $item->getName(),
                    'additional_info' => $item->getAdditionalInfo(),
                );
            }
        }

        return new ViewModel(
            array(
                'result' => $results,
            )
        );
    }

    /**
     * @return \CommonBundle\Component\Form\Form|null
     */
    private function handleLeaseForm()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return;
        }

        $form = $this->getForm('logistics_lease_add-lease');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (isset($formData['lease'])) {
                $form->setData($formData);

                if ($form->isValid()) {
                    $formData = $form->getData();

                    $item = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Item')
                        ->findOneById($formData['leaseItem']['id']);

                    $lease = new Lease(
                        $item,
                        $formData['leased_amount'],
                        new DateTime(),
                        $person,
                        $formData['leased_to'],
                        $formData['leased_pawn'],
                        $formData['comment']
                    );
                    $this->getEntityManager()->persist($lease);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The lease was successfully added!'
                    );

                    $this->redirect()->toRoute(
                        'logistics_lease',
                        array(
                            'action' => 'show',
                            'id'     => $lease->getId(),
                        )
                    );
                }
            }
        }

        return $form;
    }

    /**
     * @return \CommonBundle\Component\Form\Form|null
     */
    private function handleReturnForm()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return;
        }

        $form = $this->getForm('logistics_lease_add-return');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (isset($formData['return'])) {
                $form->setData($formData);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $item = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Item')
                        ->findOneById($data['returnItem']['id']);

                    $lease = current(
                        $this->getEntityManager()
                            ->getRepository('LogisticsBundle\Entity\Lease')
                            ->findUnreturnedByItem($item)
                    );

                    $lease->setReturned(true)
                        ->setReturnedAmount($data['returned_amount'])
                        ->setReturnedTo($person)
                        ->setReturnedDate(new DateTime())
                        ->setReturnedPawn($data['returned_pawn'])
                        ->setReturnedBy($data['returned_by'])
                        ->setReturnedComment($data['comment']);

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The return was successfully added!'
                    );

                    $this->redirect()->toRoute(
                        'logistics_lease',
                        array(
                            'action' => 'show',
                            'id'     => $lease->getId(),
                        )
                    );
                }
            }
        }

        return $form;
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->flashMessenger()->error(
                'Error',
                'No person was authenticated!'
            );

            $this->redirect()->toRoute('logistics_transport');

            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * @return Lease|null
     */
    private function getLeaseEntity()
    {
        $lease = $this->getEntityById('LogisticsBundle\Entity\Lease');

        if (!($lease instanceof Lease)) {
            $this->flashMessenger()->error(
                'Error',
                'No lease was found!'
            );

            $this->redirect()->toRoute('logistics_lease');

            return;
        }

        return $lease;
    }

    /**
     * @param  integer|null $id
     * @return Item|null
     */
    private function getItemEntity($id = null)
    {
        $id = $id ?? $this->getParam('id', 0);

        $item = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Lease\Item')
            ->findOneById($id);

        if (!($item instanceof Item)) {
            $this->flashMessenger()->error(
                'Error',
                'No lease item was found!'
            );

            $this->redirect()->toRoute('logistics_lease');

            return;
        }

        return $item;
    }
}
