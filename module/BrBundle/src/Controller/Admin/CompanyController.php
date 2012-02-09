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
	CommonBundle\Entity\Users\Credential,
	CommonBundle\Entity\Users\People\Corporate as CorporatePerson,
	CommonBundle\Entity\Users\Statuses\Corporate as CorporateStatus;

class CompanyController extends \CommonBundle\Component\Controller\ActionController
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
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
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
                $corporateRole = $this->getEntityManager()
                	->getRepository('CommonBundle\Entity\Acl\Role')
                	->findOneByName('corporate');
                
                $correspondenceContact = new CorporatePerson(
                    $formData['correspondence_contact_username'],
                    new Credential(
                        'sha512',
                        $formData['correspondence_contact_credential']
                    ),
                    array(
                   		$corporateRole 
                    ),
                    $formData['correspondence_contact_first_name'],
                    $formData['correspondence_contact_last_name'],
                    $formData['correspondence_contact_email'],
                    $formData['correspondence_contact_phone_number'],
                    $formData['correspondence_contact_sex']
                );
                
                $correspondenceStatus = new CorporateStatus(
                	$correspondenceContact, 'correspondence'
                );
                $correspondenceContact->addCorporateStatus(
                	$correspondenceStatus
                );
                
                $signatoryContact = new CorporatePerson(
                    $formData['signatory_contact_username'],
                    new Credential(
                        'sha512',
                        $formData['signatory_contact_credential']
                    ),
                    array(
                   		$corporateRole 
                    ),
                    $formData['signatory_contact_first_name'],
                    $formData['signatory_contact_last_name'],
                    $formData['signatory_contact_email'],
                    $formData['signatory_contact_phone_number'],
                    $formData['signatory_contact_sex']                    
                );
                
                $signatoryStatus = new CorporateStatus(
                	$signatoryContact, 'signatory'
                );
                $signatoryContact->addCorporateStatus(
                	$signatoryStatus
                );
                
                $newCompany = new Company(
                	$formData['company_name'],
                	$formData['vat_number'],
                	array(
                		$correspondenceContact,
                		$signatoryContact
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
        
        return array(
        	'form' => $form,
        	'companyCreated' => $companyCreated
        );
    }

    public function editAction()
    {
        
    }

    public function deleteAction()
    {
        
    }
}
