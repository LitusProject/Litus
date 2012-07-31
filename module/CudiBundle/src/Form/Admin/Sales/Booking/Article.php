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
 
namespace CudiBundle\Form\Admin\Sales\Booking;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Zend\Form\Element\Hidden,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Validator\Int as IntValidator;

/**
 * Booking by article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Article extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct( $options = null)
    {
        parent::__construct($options);
        
        $field = new Hidden('article_id');
        $field->setRequired()
            ->addValidator(new IntValidator())
            ->setAttrib('id', 'articleId')
            ->clearDecorators()
            ->setDecorators(array('ViewHelper'));
        $this->addElement($field);
         
        $field = new Text('article');
        $field->setLabel('Article')
            ->setAttrib('class', 'disableEnter')
            ->setAttrib('style', 'width: 400px;')
            ->setAttrib('id', 'articleSearch')
            ->setAttrib('autocomplete', 'off')
            ->setAttrib('data-provide', 'typeahead')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Search')
                ->setAttrib('class', 'bookings')
                ->setAttrib('id', 'search')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

    }
}
