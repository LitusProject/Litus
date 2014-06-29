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

use CommonBundle\Component\OldForm\Admin\Element\Text,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Bulk Update the Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BulkUpdate extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @var array Contains the input fields added for article quantities.
     */
    private $_inputs = array();

    /**
     * @param array           $articles
     * @param null|string|int $name     Optional name for the element
     */
    public function __construct($articles, $name = null)
    {
        parent::__construct($name);

        foreach ($articles as $article) {
            $field = new Text('article-' . $article->getId());
            $field->setValue($article->getStockValue())
                ->setAttribute('style', 'width: 70px');
            $this->add($field);

            $this->_inputs[] = $field;
        }

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach ($this->_inputs as $input) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => $input->getName(),
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'digits',
                            ),
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
