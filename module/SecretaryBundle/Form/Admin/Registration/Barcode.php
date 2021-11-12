<?php

namespace SecretaryBundle\Form\Admin\Registration;

use CommonBundle\Entity\User\Barcode as BarcodeEntity;
use CommonBundle\Entity\User\Person;
use LogicException;

/**
 * Academic Barcode form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Barcode extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Person
     */
    private $person;

    public function init()
    {
        if ($this->getPerson() === null) {
            throw new LogicException('Cannot add a barcode to a null person.');
        }

        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'options' => BarcodeEntity::$possibleTypes,
                ),
                'value'      => $this->getPerson()->getBarcode() ? $this->getPerson()->getBarcode()->getType() : '',
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'barcode',
                'label'      => 'Barcode',
                'required'   => true,
                'attributes' => array(
                    'class'     => 'disableEnter',
                    'autofocus' => true,
                ),
                'value'      => $this->getPerson()->getBarcode() ? $this->getPerson()->getBarcode()->getBarcode() : '',
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'PersonBarcode',
                                'options' => array(
                                    'person' => $this->getPerson(),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'secretary');
    }

    /**
     * @param Person
     * @return self
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}
