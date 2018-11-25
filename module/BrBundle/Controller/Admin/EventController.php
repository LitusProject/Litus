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

use BrBundle\Entity\Event;
use BrBundle\Entity\Event\CompanyMap;
use Zend\View\Model\ViewModel;

/**
 * EventController
 *
 * Controller for events organised by VTK Corporate Relations itself.
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */

class EventController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event')
                ->findAllActiveQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Event')
                ->findAllOldQuery(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('br_event_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The event was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event',
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

    public function editAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $propertiesForm = $this->getForm('br_event_edit', array('event' => $event));
        $companyMapForm = $this->getForm('br_event_companyMap');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $propertiesForm->setData($formData);
            $companyMapForm->setData($formData);

            if (isset($formData['event_edit']) && $propertiesForm->isValid()) {
                $this->flashMessenger()->success(
                    'Success',
                    'The event was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event',
                    array(
                        'action' => 'manage',
                    )
                );
            } elseif (isset($formData['event_companyMap']) && $companyMapForm->isValid()) {
                $company = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Company')
                    ->findOneById($formData['company']);
                $objectMap = new CompanyMap($company, $event);

                $this->getEntityManager()->persist($objectMap);

                $this->flashMessenger()->success(
                    'Success',
                    'The attendee was successfully added!'
                );

                $this->redirect()->toRoute(
                    'br_admin_event',
                    array(
                        'action' => 'edit',
                        'id'     => $event->getId(),
                    )
                );
            }

            $this->getEntityManager()->flush();
        }
        $eventCompanyMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\CompanyMap')
            ->findAllByEvent($event);

        return new ViewModel(
            array(
                'propertiesForm'   => $propertiesForm,
                'companyMapForm'   => $companyMapForm,
                'eventCompanyMaps' => $eventCompanyMaps,
                'event'            => $event,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $reservation = $this->getVanReservationEntity();
        if ($reservation === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($reservation);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('BrBundle\Entity\Event');

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
}
