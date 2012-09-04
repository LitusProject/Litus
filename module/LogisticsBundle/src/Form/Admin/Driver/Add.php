<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Form\Admin\Driver;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
CommonBundle\Component\Form\Admin\Element\Collection,
CommonBundle\Component\Form\Admin\Element\Hidden,
CommonBundle\Component\Form\Admin\Element\Select,
CommonBundle\Component\Form\Admin\Element\Text,
CommonBundle\Component\Validator\Uri as UriValidator,
CommonBundle\Component\Validator\Year as YearValidator,
CudiBundle\Component\Validator\SubjectCode as SubjectCodeValidator,
CudiBundle\Entity\Article,
Doctrine\ORM\EntityManager,
Zend\InputFilter\InputFilter,
Zend\InputFilter\Factory as InputFactory,
Zend\Form\Element\Submit;

/**
 * The form used to add a new Driver
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead');
        $this->add($field);
        
        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'driver_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {

            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            /*
             * TODO: proper client side validation
             */
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'name',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
            
            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }
}
