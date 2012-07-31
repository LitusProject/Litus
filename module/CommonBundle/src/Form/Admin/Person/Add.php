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
 
namespace CommonBundle\Form\Admin\Person;

use CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Validator\PhoneNumber as PhoneNumberValidator,
    CommonBundle\Component\Validator\Username as UsernameValidator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Form,
    Zend\Form\Element\Multiselect,
    Zend\Form\Element\Password,
    Zend\Form\Element\Select,
    Zend\Form\Element\Text,
    Zend\Validator\Alnum as AlnumValidator,
    Zend\Validator\Alpha as AlphaValidator,
    Zend\Validator\Identical as IdenticalValidator,
    Zend\Validator\EmailAddress as EmailAddressValidator;

/**
 * Add Person
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The form's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);
        
        $this->_entityManager = $entityManager;
        
        $field = new Text('username');
        $field->setLabel('Username')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new AlnumValidator())
            ->addValidator(new UsernameValidator($this->_entityManager));
        $this->addElement($field);

        $field = new Text('first_name');
        $field->setLabel('First Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new AlphaValidator(array('allowWhiteSpace' => true)));
        $this->addElement($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new AlphaValidator(array('allowWhiteSpace' => true)));
        $this->addElement($field);

        $field = new Text('email');
        $field->setLabel('E-mail')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new EmailAddressValidator());
        $this->addElement($field);
        
        $field = new Text('phone_number');
        $field->setLabel('Phone Number')
            ->setAttrib('placeholder', '+CCAAANNNNNN')
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new PhoneNumberValidator());
        $this->addElement($field);

        $field = new Select('sex');
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
        
        $field = new Multiselect('roles');
        $field->setLabel('Groups')
            ->setMultiOptions($this->_createRolesArray())
            ->setDecorators(array(new FieldDecorator()));
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
        $roles = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem())
                continue;
            
            $rolesArray[$role->getName()] = $role->getName();
        }
        return $rolesArray;
    }
}
