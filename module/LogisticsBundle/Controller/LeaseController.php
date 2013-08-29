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
        return new ViewModel();
    }

    public function leaseAction()
    {
        $form = new AddLeaseForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $item = $this->getEntityManager()
                        ->getRepository('LogisticsBundle\Entity\Lease\Lease')
                        ->findOneByBarcode($form['barcode']);
                $lease = new Lease(
                    $item,
                    new DateTime,
                    $this->getAuthentication()->getPersonObject(),
                    $form['leased_to'],
                    $form['leased_pawn']
                );
                $this->getEntityManager()->persist($lease);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'The lease was successfully added!'
                    )
                );

                $this->redirect()->toRoute('logistics_lease');

                return new ViewModel;
            }
        }

        return new ViewModel(
            array(
                'form'=> $form,
            )
        );
    }
}
