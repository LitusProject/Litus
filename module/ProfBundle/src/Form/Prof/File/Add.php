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
 
namespace ProfBundle\Form\Prof\File;

use CommonBundle\Component\Form\Bootstrap\Element\File as FileElement,
	CommonBundle\Component\Form\Bootstrap\Element\Text;

class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);
                
        $this->setAttrib('id', 'uploadFile');
     
        $field = new Text('description');
        $field->setLabel('Description')
			->setAttrib('size', 70)
        	->setRequired();
        $this->addElement($field);
        
        $field = new FileElement('file');
        $field->setLabel('File')
        	->setAttrib('size', 70)
        	->setRequired();
        $this->addElement($field);
    }
}