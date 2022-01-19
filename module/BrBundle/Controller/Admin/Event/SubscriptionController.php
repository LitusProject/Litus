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
namespace BrBundle\Controller\Admin\Event;


use BrBundle\Entity\Event;
use BrBundle\Entity\Event\Subscription as SubscriptionEntity;
use Laminas\View\Model\ViewModel;

/**
 * SubscriptionController
 *
 * Controller for the subscribers attending the events organised by VTK Corporate Relations itself.
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */
class SubscriptionController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function overviewAction()
    {
        $eventObject = $this->getEventEntity();
        if ($eventObject === null) {
            return new ViewModel();
        }

//        TODO: search all informative data about current visitors, total amount of connections, graph maybe

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event\Subscription')
                ->findAllByEventQuery($eventObject),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'event'              => $eventObject,
                'paginator'          => $paginator,
                'paginationControl'  => $this->paginator()->createControl(true),
            )
        );
    }


    public function deleteAction()
    {
        $this->initAjax();

        $subscription = $this->getSubscriptionEntity();
        if ($subscription === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($subscription);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function addAction()
    {
        
        $form = $this->getForm('br_event_subscription_add');
        $eventObject = $this->getEventEntity();
        $form->setEvent($eventObject);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            
            if ($form->isValid()) {
                $subscription = $form->hydrateObject();
                $subscription->setEvent($eventObject);
                $this->getEntityManager()->persist(
                    $subscription
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The Subscription was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event_subscription',
                    array(
                        'action' => 'overview',
                        'event'  => $eventObject->getId(),
                    )
                );

                return new ViewModel(array(
                    'event' => $eventObject,
                ));
            }
        }
        // TODO: Mailing should maybe only be done automatically with user subscription and not admin subscription

        return new ViewModel(
            array(
                'form' => $form,
                'event' => $eventObject,
            )
        );
    }

    public function editAction()
    {
        $subscription = $this->getSubscriptionEntity();
        if ($subscription === null) {
            return new ViewModel();
        }
        $eventObject = $this->getEventEntity();


        $form = $this->getForm('br_admin_event_subscription_edit', $subscription);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The Subscription was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event_subscription',
                    array(
                        'action' => 'overview',
                        'event'  => $eventObject->getId(),
                    )
                );

                return new ViewModel(array(
                    'event' => $eventObject,
                ));
            }
        }
        return new ViewModel(
            array(
                'form' => $form,
                'event' => $eventObject,
            )
        );
    }


    public function mailAction(){
        $subscription = $this->getSubscriptionEntity();
        if ($subscription === null) {
            return new ViewModel();
        }
        $eventObject = $this->getEventEntity();

        $mailAddress = $subscription->getEmail();
        
        //TODO: Build mail from configs
        // from
        // Title
        // Standard mail format with elements of subscriber filled in
        // QR code generator?

        // Send mail

        // Reroute

    }



    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\Event', 'event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_event',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }

    /**
     * @return Event|null
     */
    private function getSubscriptionEntity()
    {
        $subscription = $this->getEntityById('BrBundle\Entity\Event\Subscription');

        if (!($subscription instanceof SubscriptionEntity)) {
            $this->flashMessenger()->error(
                'Error',
                'No company mapping was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_event_company',
                array(
                    'action' => 'manage',
                    'event'  => $this->getEventEntity()->getId(),
                )
            );

            return;
        }

        return $subscription;
    }
}