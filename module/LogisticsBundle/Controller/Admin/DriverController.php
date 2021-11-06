<?php

namespace LogisticsBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use LogisticsBundle\Entity\Driver;

/**
 * DriverController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class DriverController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findAllQuery(),
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
        $form = $this->getForm('logistics_driver_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist(
                    $form->hydrateObject()
                );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The driver was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_driver',
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
        $driver = $this->getDriverEntity();
        if ($driver === null) {
            return new ViewModel();
        }

        $form = $this->getForm('logistics_driver_edit', $driver);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The driver was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'logistics_admin_driver',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'   => $form,
                'driver' => $driver,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $driver = $this->getDriverEntity();
        if ($driver === null) {
            return new ViewModel();
        }

        $driver->remove();
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Driver|null
     */
    private function getDriverEntity()
    {
        $driver = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findOneById($this->getParam('id'));

        if (!($driver instanceof Driver)) {
            $this->flashMessenger()->error(
                'Error',
                'No driver was found!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_driver',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $driver;
    }
}
