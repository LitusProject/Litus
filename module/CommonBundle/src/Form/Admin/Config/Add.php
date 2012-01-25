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
 
namespace CommonBundle\Form\Admin\Config;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Form\Element\Textarea;

/**
 * Add a configuration entry.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
	/**
	 * @param mixed $options The form's options
	 */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $field = new Text('prefix');
        $field->setLabel('Prefix')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Textarea('value');
        $field->setLabel('Value')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('description');
        $field->setLabel('Description')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'config_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
