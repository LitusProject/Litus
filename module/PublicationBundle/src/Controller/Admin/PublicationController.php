<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PublicationBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

class PublicationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {

        $paginator = $this->paginator()->createFromEntity(
            'PublicationBundle\Entity\Publication',
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
            // Form is being posted, persist the new driver.
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic');
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

                $driver = new Driver($person, $color);
                $driver->setYears($years);
                $this->getEntityManager()->persist($driver);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCES',
                        'The driver was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_driver',
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
                    'admin_driver',
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

        $this->getEntityManager()->remove($driver);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }
}