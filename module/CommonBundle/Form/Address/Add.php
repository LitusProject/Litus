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

namespace CommonBundle\Form\Address;

use CommonBundle\Component\Validator\NotZero as NotZeroValidator;
use CommonBundle\Entity\General\Address;

/**
 * Add Address
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'    => 'text',
            'name'    => 'street',
            'label'   => 'Street',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'    => 'text',
            'name'    => 'number',
            'label'   => 'Number',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'alnum',
                            'options' => array(
                                'allowWhiteSpace' => true,
                            ),
                        ),
                        new NotZeroValidator(),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'    => 'text',
            'name'    => 'mailbox',
            'label'   => 'Mailbox',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'    => 'text',
            'name'    => 'postal',
            'label'   => 'Postal Code',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'city',
            'label'      => 'City',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'country',
            'label'      => 'Country',
            'attributes' => array(
                'options' => $this->getCountries(),
            ),
            'value'      => 'BE',
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }

    public function setRequired($required = true)
    {
        $this->get('street')->setRequired($required);
        $this->get('number')->setRequired($required);
        $this->get('postal')->setRequired($required);
        $this->get('city')->setRequired($required);

        return parent::setRequired($required);
    }

    private function getCountries()
    {
        $options = array();
        foreach (Address::$countries as $key => $continent) {
            $options[$key] = array(
                'label' => $key,
                'options' => $continent,
            );
        }

        return $options;
    }
}
