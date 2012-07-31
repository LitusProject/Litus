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
        $form = new AddForm(
            $this->getEntityManager()
        );
        
        $companyCreated = false;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();
            
            if ($form->isValid($formData)) {                
                $newCompany = new Company(
                    $formData['company_name'],
                    $formData['vat_number'],
                    new Address(
                        $formData['address_street'],
                        $formData['address_number'],
                        $formData['address_postal'],
                        $formData['address_city'],
                        $formData['address_country']
                    )
                );
                
                $this->getEntityManager()->persist($newCompany);

                $form = new AddForm(
                    $this->getEntityManager()
                );

                $companyCreated = true;
            }
        }
        
        $this->getEntityManager()->flush();
        
        return new ViewModel(
            array(
                'form' => $form,
                'companyCreated' => $companyCreated,
            )
        );
    }

    public function editAction()
    {
        
    }

    public function deleteAction()
    {
        
    }
}
