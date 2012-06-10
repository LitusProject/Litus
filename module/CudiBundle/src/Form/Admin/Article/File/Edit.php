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
 
namespace CudiBundle\Form\Admin\Article\File;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CudiBundle\Entity\Files\Mapping as FileMapping,
	Zend\Form\Element\Submit;

/**
 * Edit File
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\File\Add
{
    public function __construct(FileMapping $mapping, $options = null)
    {
        parent::__construct($options);

        $this->removeElement('file');
        $this->removeElement('submit');
        
		$field = new Submit('submit');
        $field->setLabel('Save')
                ->setAttrib('class', 'file_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populateFromFile($mapping);
    }
}