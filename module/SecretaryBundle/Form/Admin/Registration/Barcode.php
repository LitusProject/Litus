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

namespace SecretaryBundle\Form\Admin\Registration;

use CommonBundle\Entity\User\Barcode as BarcodeEntity,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Person\Barcode as BarcodeValidator,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Academic Barcode form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Barcode extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var Person The person we're going to assign a barcode
     */
    protected $_person = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Person          $person        The person we're going to assign a barcode
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Person $person, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_person = $person;

        $field = new Select('type');
        $field->setLabel('Type')
            ->setAttribute(
                'options',
                BarcodeEntity::$possibleTypes
            )
            ->setValue($person->getBarcode()->getType());
        $this->add($field);

        $field = new Text('barcode');
        $field->setLabel('Barcode')
            ->setAttribute('class', 'disableEnter')
            ->setAttribute('autofocus', true)
            ->setRequired()
            ->setValue($person->getBarcode() ? $person->getBarcode()->getBarcode() : '');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'secretary');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'barcode',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new BarcodeValidator($this->_entityManager, $this->_person),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
