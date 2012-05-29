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
 
namespace ProfBundle\Form\Prof\Prof;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
	CommonBundle\Component\Form\Bootstrap\Element\Text,
	Zend\Form\Element\Hidden,
	Zend\Validator\Int as IntValidator;

/**
 * Add Prof
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
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
		    ->setAttrib('class', $field->getAttrib('class') . ' input-xlarge')
			->setAttrib('id', 'profSearch')
			->setAttrib('autocomplete', 'off')
			->setAttrib('data-provide', 'typeahead')
			->setRequired();
		$this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit'));
    }
}