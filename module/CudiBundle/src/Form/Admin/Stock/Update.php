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

namespace CudiBundle\Form\Admin\Stock;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CudiBundle\Entity\Sales\Article,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Form\Element\Textarea,
    Zend\Validator\Int as IntValidator,
    Zend\Validator\GreaterThan as GreaterThanValidator;

/**
 * Update Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Update extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct(Article $article, $options = null)
    {
        parent::__construct($options);

        $field = new Text('number');
        $field->setLabel('Number')
            ->setAttrib('autocomplete', 'off')
            ->setRequired()
            ->addValidator(new IntValidator())
            ->addValidator(new GreaterThanValidator(0))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Textarea('comment');
        $field->setLabel('Comment')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('updateStock');
        $field->setLabel('Update')
                ->setAttrib('class', 'stock_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->populate(array(
                'number' => $article->getStockValue()
            )
        );
    }
}
