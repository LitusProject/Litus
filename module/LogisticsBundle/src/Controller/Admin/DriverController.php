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

use LogisticsBundle\Entity\Driver;

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
                'paginationControl' => $this->paginator()->createControl(true),
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
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            
            if ($form->isValid()) {
                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic');
                if ($formData['person_id'] == '') {
                    /*
                     * No autocompletion used, we assume the user ID was entered
                     */
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

                $driver = new Driver($person, $years);
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
}