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
 
namespace CudiBundle\Form\Admin\Mail;
	
use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Zend\Form\Element\Hidden,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Form\Element\Textarea,
	Zend\Validator\EmailAddress as EmailAddressValidator;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Send extends \CommonBundle\Component\Form\Admin\Form
{
	public function __construct($email = null, $name = null, $opts = null)
    {
        parent::__construct($opts);
                
        $field = new Hidden('email');
        $field->setValue($email)
            ->setRequired()
            ->addValidator(new EmailAddressValidator())
            ->clearDecorators()
            ->setDecorators(array('ViewHelper'));
        $this->addElement($field);
        
        $field = new Hidden('name');
        $field->setValue($name)
            ->setRequired()
            ->clearDecorators()
            ->setDecorators(array('ViewHelper'));
        $this->addElement($field);
        
        $field = new Text('subject');
        $field->setLabel('Subject')
			->setAttrib('style', 'width: 400px')
            ->setRequired();
        $this->addElement($field);
         
        $field = new Textarea('message');
        $field->setLabel('Message')
            ->setAttrib('style', 'width: 500px')
        	->setRequired();
        $this->addElement($field);
    }
}
