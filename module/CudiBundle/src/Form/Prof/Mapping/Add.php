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
 
namespace CudiBundle\Form\Prof\Mapping;

use CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    Zend\Form\Element\Hidden,
    Zend\Validator\Int as IntValidator;

/**
 * Add Mapping
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
         
        $field = new Hidden('article_id');
        $field->setRequired()
            ->addValidator(new IntValidator())
            ->setAttrib('id', 'articleId');
        $this->addElement($field);
         
        $field = new Text('article');
        $field->setLabel('Article')
            ->setAttrib('class', $field->getAttrib('class') . ' input-xxlarge')
            ->setAttrib('id', 'articleSearch')
            ->setAttrib('autocomplete', 'off')
            ->setAttrib('data-provide', 'typeahead')
            ->setRequired();
        $this->addElement($field);
        
        $field = new Checkbox('mandatory');
        $field->setLabel('Mandatory');
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit'));
    }
}
