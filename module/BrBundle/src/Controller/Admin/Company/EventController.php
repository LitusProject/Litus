<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace BrBundle\Controller\Admin\Company;

use BrBundle\Entity\Company\Event,
    BrBundle\Form\Admin\Company\Event\Add as AddForm,
    BrBundle\Form\Admin\Company\Event\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * EventController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EventController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();
            
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Event')
                ->findAllByCompany($company),
            $this->getParam('page')
        );
        
        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'company' => $company,
            )
        );
    }
    
    public function addAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();
            
        $form = new AddForm();
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {                
                $event = new Event(
                    $formData['event_name'],
                    $formData['location'],
                    DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']),
                    DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']),
                    $formData['description'],
                    $company
                );
                
                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_company_event',
                    array(
                        'action' => 'manage',
                        'id' => $company->getId(),
                    )
                );
                
                return new ViewModel();
            }
        }
            
        return new ViewModel(
            array(
                'company' => $company,
                'form' => $form,
            )
        );
    }
    
    public function editAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();
            
        $form = new EditForm($event);
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {
                $event->setName($formData['event_name'])
                    ->setLocation($formData['location'])
                    ->setStartDate(DateTime::createFromFormat('d#m#Y H#i', $formData['start_date']))
                    ->setEndDate(DateTime::createFromFormat('d#m#Y H#i', $formData['end_date']))
                    ->setDescription($formData['description']);
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_company_event',
                    array(
                        'action' => 'manage',
                        'id' => $event->getCompany()->getId(),
                    )
                );
                
                return new ViewModel();
            }
        }
                
        return new ViewModel(
            array(
                'company' => $event->getCompany(),
                'form' => $form,
            )
        );
    }
    
    public function deleteAction()
    {
        $this->initAjax();
                
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();
        
        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }
    
    private function _getCompany()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the company!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );
            
            return;
        }
    
        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));
        
        if (null === $company) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No company with the given ID was found!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );
            
            return;
        }
        
        return $company;
    }
    
    private function _getEvent()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the event!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );
            
            return;
        }
    
        $event = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Event')
            ->findOneById($this->getParam('id'));
        
        if (null === $event) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No event with the given ID was found!'
                )
            );
            
            $this->redirect()->toRoute(
                'admin_company',
                array(
                    'action' => 'manage'
                )
            );
            
            return;
        }
        
        return $event;
    }
}
