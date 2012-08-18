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
 
namespace CalendarBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Admin\Decorator\FileDecorator,
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Zend\Form\Element\File,
    Zend\Form\Element\Submit,
    Zend\Validator\File\Extension as ExtensionValidator,
    Zend\Validator\File\Size as FileSizeValidator;

/**
 * Event poster form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Poster extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param mixed $options The form's options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        
        $field = new File('poster');
        $field->setLabel('Poster')
            ->setDecorators(array(new FileDecorator()))
            ->setRequired()
            ->addValidator(new ExtensionValidator(array('jpg', 'png')))
            ->addValidator(new FileSizeValidator('2MB'));
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Save')
            ->setAttrib('class', 'image_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
