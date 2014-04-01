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

namespace CudiBundle\Form\Admin\Stock;

use CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CudiBundle\Entity\Sale\Article,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Update Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Update extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param Article         $article
     * @param null|string|int $name    Optional name for the element
     */
    public function __construct(Article $article, $name = null)
    {
        parent::__construct($name);

        $field = new Text('number');
        $field->setLabel('Number')
            ->setAttribute('autocomplete', 'off')
            ->setRequired();
        $this->add($field);

        $field = new Textarea('comment');
        $field->setLabel('Comment')
            ->setRequired();
        $this->add($field);

        $field = new Submit('updateStock');
        $field->setValue('Update')
            ->setAttribute('class', 'stock_edit');
        $this->add($field);

        $this->setData(
            array(
                'number' => $article->getStockValue()
            )
        );
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'number',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                        array(
                            'name' => 'greaterthan',
                            'options' => array(
                                'min' => 0,
                                'inclusive' => true,
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'comment',
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
