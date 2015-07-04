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

use DateTime,
    LogisticsBundle\Component\Controller\LogisticsController,
    LogisticsBundle\Entity\Lease\Item,
    LogisticsBundle\Entity\Lease\Lease,
    Zend\View\Model\ViewModel;

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

        $leaseForm = $this->handleLeaseForm();
        $returnForm = $this->handleReturnForm();

        return new ViewModel(
            array(
                'leases' => $paginator,
                'paginationControl' =>  $this->paginator()->createControl(),
                'leaseForm' => $leaseForm,
                'returnForm' => $returnForm,
            )
        );
    }

    public function showAction()
    {
        if (!($lease = $this->getLeaseEntity())) {
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
        if (!($item = $this->getItemEntity($this->getRequest()->getQuery('searchItem')['id']))) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                ->findByItemQuery($item),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'item' => $item,
                'leases' => $paginator,
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
                ->getRepository('LogisticsBundle\Entity\Lease\Lease');

            foreach ($items as $item) {
                if ($purpose === 'lease' && count($leaseRepo->findUnreturnedByItem($item)) > 0) {
                    continue;
                }
                if ($purpose === 'return' && count($leaseRepo->findUnreturnedByItem($item)) <= 0) {
                    continue;
                }

                $results[] = array(
                    'id' => $item->getId(),
                    'value' => $item->getName(),
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
     * @return null|\CommonBundle\Component\Form\Form
     */
    private function handleLeaseForm()
    {
        if (!($person = $this->getPersonEntity())) {
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
                            'id' => $lease->getId(),
                        )
                    );
                }
            }
        }

        return $form;
    }

    /**
     * @return null|\CommonBundle\Component\Form\Form
     */
    private function handleReturnForm()
    {
        if (!($person = $this->getPersonEntity())) {
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

                    $lease = current($this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                        ->findUnreturnedByItem($item));

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
                            'id' => $lease->getId(),
                        )
                    );
                }
            }
        }

        return $form;
    }

    /**
     * @return null|\CommonBundle\Entity\User\Person
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->flashMessenger()->error(
                'Error',
                'No person was authenticated!'
            );

            $this->redirect()->toRoute('logistics_index');

            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * @return Lease|null
     */
    private function getLeaseEntity()
    {
        $lease = $this->getEntityById('LogisticsBundle\Entity\Lease\Lease');

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
     * @param  int|null  $id
     * @return Item|null
     */
    private function getItemEntity($id = null)
    {
        $id = $id === null ? $this->getParam('id', 0) : $id;

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
