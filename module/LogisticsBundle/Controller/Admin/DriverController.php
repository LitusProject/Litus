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

namespace LogisticsBundle\Controller\Admin;

use LogisticsBundle\Entity\Driver,
    Zend\View\Model\ViewModel;

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
                'paginator' => $paginator,
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
        if (!($driver = $this->_getDriver())) {
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
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($driver = $this->_getDriver())) {
            return new ViewModel();
        }

        $driver->setRemoved(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Driver
     */
    private function _getDriver()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the driver!'
            );

            $this->redirect()->toRoute(
                'logistics_admin_driver',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $driver = $this->getEntityManager()
        ->getRepository('LogisticsBundle\Entity\Driver')
        ->findOneById($this->getParam('id'));

        if (null === $driver) {
            $this->flashMessenger()->error(
                'Error',
                'No driver with the given ID was found!'
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
