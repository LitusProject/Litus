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
 
namespace CommonBundle\Form\Admin\Academic;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Component\Validator\PhoneNumber as PhoneNumberValidator,
	CommonBundle\Component\Validator\Username as UsernameValidator,
	Doctrine\ORM\EntityManager,
	Zend\Form\Form,
	Zend\Form\Element\Multiselect,
	Zend\Form\Element\Password,
	Zend\Form\Element\Select,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Validator\Alnum as AlnumValidator,
	Zend\Validator\Alpha as AlphaValidator,
	Zend\Validator\Identical as IdenticalValidator,
	Zend\Validator\EmailAddress as EmailAddressValidator;

/**
 * Add a user to the database.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	private $_entityManager = null;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param string $fieldPrefix A prefix attached to all field names
	 * @param mixed $opts The validator's options
	 */
    public function __construct(EntityManager $entityManager, $fieldPrefix = '', $opts = null)
    {
        parent::__construct($opts);
		
		$this->_entityManager = $entityManager;
		
        $field = new Text(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'username');
        $field->setLabel('Username')
        	->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new AlnumValidator())
            ->addValidator(new UsernameValidator($this->_entityManager));
        $this->addElement($field);

        $field = new Password(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'credential');
        $field->setLabel('Password')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Password(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'verify_credential');
        $field->setLabel('Repeat Password')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new IdenticalValidator(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'credential'));
        $this->addElement($field);

		$field = new Text(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'first_name');
        $field->setLabel('First Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new AlphaValidator(array('allowWhiteSpace' => true)));
        $this->addElement($field);

        $field = new Text(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'last_name');
        $field->setLabel('Last Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new AlphaValidator(array('allowWhiteSpace' => true)));
        $this->addElement($field);

        $field = new Text(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'email');
        $field->setLabel('E-mail')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new EmailAddressValidator());
        $this->addElement($field);
        
        $field = new Text(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'phone_number');
        $field->setLabel('Phone Number')
            ->setAttrib('placeholder', '+CCAAANNNNNN')
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new PhoneNumberValidator());
        $this->addElement($field);

		$field = new Select(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'sex');
		$field->setLabel('Sex')
			->setRequired()
			->setMultiOptions(
					array(
						'm' => 'M',
						'f' => 'F'
					)
				)
			->setDecorators(array(new FieldDecorator()));
		$this->addElement($field);

        $field = new Multiselect(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'roles');
        $field->setLabel('Groups')
            ->setMultiOptions($this->_createRolesArray())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Text(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'university_identification');
        $field->setLabel('University Identification')
        	->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new AlnumValidator());
        $this->addElement($field);

        $field = new Submit(('' == $fieldPrefix ? '' : $fieldPrefix . '_') . 'submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'students_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
	
	/**
	 * Returns an array that has all the roles, so that they are available in the
	 * roles multiselect.
	 *
	 * @return array
	 */
    private function _createRolesArray()
    {
        $hiddenRoles = array(
            'guest',
            'corporate'
        );

        $roles = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        $rolesArray = array();
        foreach ($roles as $role) {
            if (in_array($role->getName(), $hiddenRoles))
                continue;
            
            $rolesArray[$role->getName()] = $role->getName();
        }
        return $rolesArray;
    }
}
