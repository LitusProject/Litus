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

namespace CommonBundle\Form\Account;

use CommonBundle\Component\OldForm\Bootstrap\Element\File,
    CommonBundle\Component\OldForm\Bootstrap\Element\Hidden,
    CommonBundle\Component\OldForm\Bootstrap\Element\Submit,
    Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Upload Profile Image
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Profile extends \CommonBundle\Component\OldForm\Bootstrap\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('class', 'form-inline');

        $field = new Hidden('x');
        $field->setValue(0);
        $this->add($field);

        $field = new Hidden('y');
        $field->setValue(0);
        $this->add($field);

        $field = new Hidden('x2');
        $field->setValue(0);
        $this->add($field);

        $field = new Hidden('y2');
        $field->setValue(0);
        $this->add($field);

        $field = new Hidden('w');
        $field->setValue(0);
        $this->add($field);

        $field = new Hidden('h');
        $field->setValue(0);
        $this->add($field);

        $field = new File('profile');
        $field->setAttribute('data-type', 'small');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'x',
                    'required' => false,
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
                    'name'     => 'y',
                    'required' => false,
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
                    'name'     => 'x2',
                    'required' => false,
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
                    'name'     => 'y2',
                    'required' => false,
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
                    'name'     => 'w',
                    'required' => false,
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
                    'name'     => 'h',
                    'required' => false,
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
                    'name'     => 'profile',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'fileextension',
                            'options' => array(
                                'extension' => 'jpg,png',
                            ),
                        ),
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'max' => '2MB',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
