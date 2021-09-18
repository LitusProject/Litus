<?php

namespace LogisticsBundle\Controller\Admin;

use CommonBundle\Entity\User\Person\Academic;
use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Consumptions as Consumptions;

/**
 * ConsumptionsController
 */
class ConsumptionsController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Consumptions')
                ->findAll(),
            $this->getParam('page')
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
        $form = $this->getForm('logistics_consumptions_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The consumptions were succesfully created!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_consumptions',
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

    public function deleteAction()
    {
        $this->initAjax();

        $consumptions = $this->getConsumptionsEntity();

        if ( $consumptions === null) {
//            echo json_encode("test");
//            die();
            return new ViewModel();
        }

        $this->getEntityManager()->remove($consumptions);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'succes'),
            )
        );
    }

    private function getConsumptionsEntity()
    {
        $consumptions = $this->getEntityById('LogisticsBundle\Entity\Consumptions');

        if (!($consumptions instanceof Consumptions)) {
            $this->flashMessenger()->error(
                'Error',
                'No consumptions were found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_consumptions',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $consumptions;
    }
}