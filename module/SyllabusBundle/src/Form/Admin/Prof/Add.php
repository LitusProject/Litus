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
 
namespace SyllabusBundle\Form\Admin\Prof;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	Zend\Form\Element\Hidden,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Validator\Int as IntValidator;

class Add extends \CommonBundle\Component\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);
         
		$field = new Hidden('prof_id');
		$field->setRequired()
		    ->addValidator(new IntValidator())
		    ->setAttrib('id', 'profId');
		$this->addElement($field);
		 
		$field = new Text('prof');
		$field->setLabel('Docent')
			->setAttrib('size', 70)
			->setAttrib('id', 'profSearch')
			->setAttrib('autocomplete', 'off')
			->setAttrib('data-provide', 'typeahead')
			->setRequired()
			->setDecorators(array(new FieldDecorator()));
		$this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'docent_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

    }
}