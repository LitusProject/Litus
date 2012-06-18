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
 
namespace CudiBundle\Form\Admin\Article\Mapping;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	Zend\Form\Element\Hidden,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Form\Element\Checkbox,
	Zend\Validator\Int as IntValidator;

/**
 * Add Mapping
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct($opts = null)
    {
        parent::__construct($opts);
                 
        $field = new Hidden('subject_id');
        $field->setRequired()
            ->addValidator(new IntValidator())
            ->setAttrib('id', 'subjectId')
            ->clearDecorators()
            ->setDecorators(array('ViewHelper'));
        $this->addElement($field);
         
        $field = new Text('subject');
        $field->setLabel('Subject')
			->setAttrib('size', 70)
			->setAttrib('id', 'subjectSearch')
			->setAttrib('autocomplete', 'off')
			->setAttrib('data-provide', 'typeahead')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Checkbox('mandatory');
        $field->setLabel('Mandatory')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'article_subject_mapping_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}