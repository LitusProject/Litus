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

use CommonBundle\Component\FlashMessenger\FlashMessage,
    LogisticsBundle\Entity\Driver,
    LogisticsBundle\Form\Admin\Driver\Add,
    LogisticsBundle\Form\Admin\Driver\Edit,
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
        $form = new Add($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');
                if ($formData['person_id'] == '') {
                    // No autocompletion used, we assume the username was entered
                    $person = $repository->findOneByUsername($formData['person_name']);
                } else {
                    $person = $repository->findOneById($formData['person_id']);
                }

                $yearIds = $formData['years'];
                $years = array();
                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\AcademicYear');
                foreach($yearIds as $yearId) {
                    $years[] = $repository->findOneById($yearId);
                }

                $color = $formData['color'];

                $driver = $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Driver')
                    ->findOneByPerson($person);

                if (!$driver) {
                    $driver = new Driver($person, $color);
                    $this->getEntityManager()->persist($driver);
                } else {
                    $driver->setRemoved(false);
                }

                $driver->setYears($years);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The driver was succesfully created!'
                    )
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
        if (!($driver = $this->_getDriver()))
            return new ViewModel();

        $form = new Edit($this->getEntityManager(), $driver);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $yearIds = $formData['years'];
                $years = array();
                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\AcademicYear');
                foreach($yearIds as $yearId) {
                    $years[] = $repository->findOneById($yearId);
                }

                $driver->setColor($formData['color']);
                $driver->setYears($years);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The driver was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'logistics_admin_driver',
                    array(
                        'action' => 'manage'
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

        if (!($driver = $this->_getDriver()))
            return new ViewModel();

        $driver->setRemoved(true);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getDriver()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the driver!'
                )
            );

            $this->redirect()->toRoute(
                'logistics_admin_driver',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $driver = $this->getEntityManager()
        ->getRepository('LogisticsBundle\Entity\Driver')
        ->findOneById($this->getParam('id'));

        if (null === $driver) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No driver with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'logistics_admin_driver',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $driver;
    }
}
