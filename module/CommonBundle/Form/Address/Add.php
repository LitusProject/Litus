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

namespace CommonBundle\Form\Address;

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

        $this->addClass('address');

        $this->add(array(
            'type'       => 'text',
            'name'       => 'street',
            'label'      => 'Street',
            'attributes' => array(
                'class' => 'street',
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'number',
            'label'      => 'Number',
            'attributes' => array(
                'class' => 'number',
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name'    => 'Alnum',
                            'options' => array(
                                'allowWhiteSpace' => true,
                            ),
                        ),
                        array('name' => 'NotZero'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'mailbox',
            'label'      => 'Mailbox',
            'attributes' => array(
                'class' => 'mailbox',
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'postal',
            'label'      => 'Postal Code',
            'attributes' => array(
                'class' => 'postal',
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name'    => 'Alnum',
                            'options' => array(
                                'allowWhiteSpace' => true,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'city',
            'label'      => 'City',
            'attributes' => array(
                'class' => 'city',
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
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
                'class'   => 'country',
                'options' => $this->getCountries(),
            ),
            'value'   => 'BE',
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }

    public function setRequired($required = true)
    {
        /** @var \CommonBundle\Component\Form\Bootstrap\Element $street */
        $street = $this->get('street');
        /** @var \CommonBundle\Component\Form\Bootstrap\Element $number */
        $number = $this->get('number');
        /** @var \CommonBundle\Component\Form\Bootstrap\Element $mailbox */
        $mailbox = $this->get('mailbox');
        /** @var \CommonBundle\Component\Form\Bootstrap\Element $postal */
        $postal = $this->get('postal');
        /** @var \CommonBundle\Component\Form\Bootstrap\Element $city */
        $city = $this->get('city');

        $street->setRequired($required);
        $number->setRequired($required);
        $mailbox->setRequired(false);
        $postal->setRequired($required);
        $city->setRequired($required);

        return $this;
    }

    private function getCountries()
    {
        $options = array();
        foreach (Address::$countries as $key => $continent) {
            $options[$key] = array(
                'label'   => $key,
                'options' => $continent,
            );
        }

        return $options;
    }
}
