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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage;

use LogisticsBundle\Form\Admin\Driver\Add;

use \Zend\View\Model\ViewModel;

class DriverController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
            )
        );
    }

    public function addAction()
    {
        $form = new Add($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            /*
             * Form is being posted, persist the new driver.
            */

            // TODO: persist

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

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}