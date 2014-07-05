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

namespace MailBundle\Form\Admin\MailingList\Entry\Person;

use CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Academic as AcademicValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Academic
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Academic extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instanc
     */
    protected $_entityManager = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $academic = new Collection('academic');
        $academic->setLabel('Add Academic');
        $this->add($academic);

        $this->_entityManager = $entityManager;

        $field = new Text('person_name');
        $field->setLabel('Name')
            ->setRequired(true)
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead');
        $academic->add($field);

        $field = new Hidden('person_id');
        $field->setAttribute('id', 'personId');
        $academic->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'mail_add');
        $academic->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        if (!isset($this->data['person_id']) || '' == $this->data['person_id']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'person_name',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new AcademicValidator(
                                $this->_entityManager,
                                array(
                                    'byId' => false,
                                )
                            )
                        ),
                    )
                )
            );
        } else {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'person_id',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new AcademicValidator(
                                $this->_entityManager,
                                array(
                                    'byId' => true,
                                )
                            )
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
