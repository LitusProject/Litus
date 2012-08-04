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
 
namespace BrBundle\Controller\Admin;

use BrBundle\Entity\Company,
    BrBundle\Form\Admin\Company\Add as AddForm,
    BrBundle\Form\Admin\Company\Edit as EditForm,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Address,
    Zend\View\Model\ViewModel;

/**
 * CompanyController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class CompanyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'BrBundle\Entity\Company',
            $this->getParam('page'),
            array(
                'active' => true
            )
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
        $form = new AddForm($this->getEntityManager());
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {                
                $company = new Company(
                    $formData['company_name'],
                    $formData['vat_number'],
                    new Address(
                        $formData['address_street'],
                        $formData['address_number'],
                        $formData['address_postal'],
                        $formData['address_city'],
                        $formData['address_country']
                    ),
                    $formData['history'],
                    $formData['description'],
                    $formData['sector']
                );
                
                $this->getEntityManager()->persist($company);
                $this->getEntityManager()->flush();
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The company was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_company',
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

    public function editAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();
            
        $form = new EditForm($this->getEntityManager(), $company);
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {
                $company->setName($formData['company_name'])
                    ->setVatNumber($formData['vat_number'])
                    ->setHistory($formData['history'])
                    ->setDescription($formData['description'])
                    ->setSector($formData['sector'])
                    ->getAddress()
                        ->setStreet($formData['address_street'])
                        ->setNumber($formData['address_number'])
                        ->setPostal($formData['address_postal'])
                        ->setCity($formData['address_city'])
                        ->setCountry($formData['address_country']);
                
                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The company was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_company',
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
                'company' => $company,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();
                
        if (!($company = $this->_getCompany()))
            return new ViewModel();

        $company->deactivate();
        $this->getEntityManager()->flush();
        
        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }
    
    public function logoAction()
    {
        if (!($company = $this->_getCompany()))
            return new ViewModel();
        
        return new ViewModel();
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
    
        $user = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($this->getParam('id'));
        
        if (null === $user) {
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
        
        return $user;
    }
}
