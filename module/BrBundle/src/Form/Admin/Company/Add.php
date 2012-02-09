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
 
namespace BrBundle\Form\Admin\Company;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Form\Admin\User\Add as ContactForm,
	Doctrine\ORM\EntityManager,
	Zend\Form\Form,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text;

/**
 * Add a company.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param mixed $opts The validator's options
	 */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);
		
        $field = new Text('company_name');
        $field->setLabel('Company Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('vat_number');
        $field->setLabel('VAT Number')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $this->addDisplayGroup(
            array(
                'company_name',
                'vat_number',
            ),
            'company_information'
        );
        $this->getDisplayGroup('company_information')
            ->setLegend('Company Information')
            ->setAttrib('id', 'company_information')
            ->removeDecorator('DtDdWrapper');
            
        $contactPerson = new ContactForm($entityManager, 'correspondence_contact');
        
        $contactPerson->removeElement('correspondence_contact_roles');
        $contactPerson->removeElement('correspondence_contact_submit');
        
        $this->addElements(
        	$contactPerson->getElements()
        );
        
        $this->addDisplayGroup(
            array(
                'correspondence_contact_username',
                'correspondence_contact_credential',
                'correspondence_contact_verify_credential',
                'correspondence_contact_first_name',
                'correspondence_contact_last_name',
                'correspondence_contact_email',
                'correspondence_contact_phone_number',
                'correspondence_contact_sex',
            ),
            'correspondence_contact'
        );
        $this->getDisplayGroup('correspondence_contact')
            ->setLegend('Correspondence Contact')
            ->setAttrib('id', 'correspondence_contact')
            ->removeDecorator('DtDdWrapper');
            
        $contactPerson = new ContactForm($entityManager, 'signatory_contact');
        
        $contactPerson->removeElement('signatory_contact_roles');
        $contactPerson->removeElement('signatory_contact_submit');
        
        $this->addElements(
        	$contactPerson->getElements()
        );
        
        $this->addDisplayGroup(
            array(
                'signatory_contact_username',
                'signatory_contact_credential',
                'signatory_contact_verify_credential',
                'signatory_contact_first_name',
                'signatory_contact_last_name',
                'signatory_contact_email',
                'signatory_contact_phone_number',
                'signatory_contact_sex',
            ),
            'signatory_contact'
        );
        $this->getDisplayGroup('signatory_contact')
            ->setLegend('Signatory Contact')
            ->setAttrib('id', 'signatory_contact')
            ->removeDecorator('DtDdWrapper');

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'companies_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
