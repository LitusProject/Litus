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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Form\Admin\Registration;

use CommonBundle\Entity\User\Barcode as BarcodeEntity,
    CommonBundle\Entity\User\Person,
    LogicException;

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
        if (null === $this->getPerson()) {
            throw new LogicException('Cannot add a barcode to a null person.');
        }

        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'type',
            'label'      => 'Type',
            'required'   => true,
            'attributes' => array(
                'options' => BarcodeEntity::$possibleTypes,
            ),
            'value'      => $this->getPerson()->getBarcode() ? $this->getPerson()->getBarcode()->getType() : '',
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'barcode',
            'label'      => 'Barcode',
            'required'   => true,
            'attributes'  => array(
                'class'     => 'disableEnter',
                'autofocus' => true,
            ),
            'value'      => $this->getPerson()->getBarcode() ? $this->getPerson()->getBarcode()->getBarcode() : '',
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'person_barcode',
                            'options' => array(
                                'person' => $this->getPerson(),
                            ),
                        ),
                    ),
                ),
            ),
        ));

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
