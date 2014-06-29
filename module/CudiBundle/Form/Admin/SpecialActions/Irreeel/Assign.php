<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\SpecialActions\Irreeel;

use CommonBundle\Component\OldForm\Admin\Element\Checkbox,
    CommonBundle\Component\OldForm\Admin\Element\Hidden,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Assign Ir.Reëel
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Assign extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Hidden('article_id');
        $field->setAttribute('id', 'articleId');
        $this->add($field);

        $field = new Text('article');
        $field->setLabel('Article')
            ->setAttribute('class', 'disableEnter')
            ->setAttribute('style', 'width: 400px;')
            ->setAttribute('id', 'articleSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('only_cudi');
        $field->setLabel('Only Cudi')
            ->setValue(true);
        $this->add($field);

        $field = new Checkbox('send_mail');
        $field->setLabel('Send Mail');
        $this->add($field);

        $field = new Checkbox('test');
        $field->setLabel('Test')
            ->setValue(true);
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Assign')
            ->setAttribute('class', 'action');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'article_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'article',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
