<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Booking;

use    CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Book textbooks
 *
 * @author Niels Avonds<niels.avonds@litus.cc>
 */
class Booking extends \CommonBundle\Component\Form\Bootstrap\Form
{

    /**
     * The maximum number allowed to enter in the textbook booking form.
     */
    const MAX_BOOKING_NUMBER = 5;

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var array Contains the input fields added for article quantities.
     */
    private $_inputs = array();

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $field = new Submit('submit');
        $field->setValue('Book')
            ->setAttribute('class', 'btn btn-primary pull-right');
        $this->add($field);
    }

    public function addInputsForArticles($articles)
    {
        foreach ($articles as $article) {
            $saleArticle = $article['article'];

            $field = new Text('article-' . $saleArticle->getId());
            $field->setAttribute('class', 'input-very-mini')
                ->setAttribute('placeholder', '0');
            $this->add($field);

            $this->_inputs[] = $field;
        }
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
                            array(
                                'name' => 'between',
                                'options' => array(
                                    'min' => 0,
                                    'max' => self::MAX_BOOKING_NUMBER,
                                ),
                            ),
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
