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

namespace CudiBundle\Form\Sale\Sale;

use CommonBundle\Component\Validator\Username as UsernameValidator,
    CommonBundle\Component\Form\Bootstrap\Element\Reset,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CudiBundle\Component\Validator\ArticleBarcode as ArticleBarcodeValidator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Hidden,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Return Sale
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ReturnSale extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null )
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Hidden('person_id');
        $field->setAttribute('id', 'personId');
        $this->add($field);

        $field = new Text('person');
        $field->setLabel('Person')
            ->setAttribute('placeholder', 'Student')
            ->setAttribute('style', 'width: 400px;')
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $this->add($field);

        $field = new Text('article');
        $field->setLabel('Article')
            ->setRequired()
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('placeholder', 'Article Barcode');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Return')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('id', 'signin');
        $this->add($field);

        $field = new Reset('cancel');
        $field->setValue('Cancel');
        $this->add($field);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'person_id',
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
                        'name'     => 'person',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
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
                        'validators' => array(
                            new ArticleBarcodeValidator($this->_entityManager),
                        ),
                    )
                )
            );

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
