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
 
namespace MailBundle\Form\Admin\Cudi;
    
use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Zend\Form\Element\Checkbox,
    Zend\Form\Element\Submit;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct($opts = null)
    {
        parent::__construct($opts);
        
        $field = new Checkbox('test_it');
        $field->setLabel('Test to IT')
            ->setValue(true)
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Send Mail')
                ->setAttrib('class', 'mail')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
