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

/**
 * Add Address
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddPrimary extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        $this->addClass('primary-address');

        list($cities, $streets) = $this->getCities();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'city',
            'label'      => 'City',
            'attributes' => array(
                'options' => $cities,
                'class'   => 'city',
            ),
            'options' => array(
                'input' => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'notEmpty',
                            'options' => array(
                                'zero', 'string',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'other',
            'attributes' => array(
                'class' => 'other',
            ),
            'elements'   => array(
                array(
                    'type'    => 'text',
                    'name'    => 'postal',
                    'label'   => 'Postal Code',
                    'options' => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'alnum',
                                'options' => array(
                                    'allowWhiteSpace' => true,
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'city',
                    'label'    => 'City',
                    'required' => true,
                    'options'  => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'    => 'text',
                    'name'    => 'street',
                    'label'   => 'Street',
                    'options' => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'notEmpty'),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $streetSelects = array();

        foreach ($streets as $id => $collection) {
            $streetSelects[] = array(
                'type'       => 'select',
                'name'       => 'street_' . $id,
                'label'      => 'Street',
                'attributes' => array(
                    'class'   => 'street street-' . $id,
                    'options' => $collection,
                ),
                'options' => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'notEmpty',
                                'options' => array(
                                    'zero', 'string',
                                ),
                            ),
                        ),
                    ),
                ),
            );
        }

        $this->add(array(
            'type'     => 'fieldset',
            'name'     => 'street',
            'elements' => $streetSelects,
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'number',
            'label'      => 'Number',
            'attributes' => array(
                'class' => 'number',
            ),
            'options'    => array(
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
                        array('name' => 'not_zero'),
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
        parent::setRequired($required);

        /** @var \CommonBundle\Component\Form\Bootstrap\Element $street */
        $street = $this->get('street');
        /** @var \CommonBundle\Component\Form\Bootstrap\Element $number */
        $number = $this->get('number');
        /** @var \CommonBundle\Component\Form\Bootstrap\Element $mailbox */
        $mailbox = $this->get('mailbox');
        /** @var \CommonBundle\Component\Form\Bootstrap\Element $city */
        $city = $this->get('city');
        /** @var \CommonBundle\Component\Form\Fieldset $other */
        $other = $this->get('other');

        $street->setRequired($required);
        $number->setRequired($required);
        $mailbox->setRequired(false);
        $city->setRequired($required);

        $other->setRequired($required);

        return $this;
    }

    private function getCities()
    {
        if (null !== $this->getCache()) {
            if (null !== ($result = $this->getCache()->getItem('Litus_CommonBundle_Entity_General_Address_Cities_Streets'))) {
                return $result;
            }
        }

        $cities = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address\City')
            ->findAll();

        $optionsCity = array('' => '');
        $optionsStreet = array();
        foreach ($cities as $city) {
            $optionsCity[$city->getId()] = $city->getPostal() . ' ' . $city->getName();
            $optionsStreet[$city->getId()] = array('' => '');

            foreach ($city->getStreets() as $street) {
                $optionsStreet[$city->getId()][$street->getId()] = $street->getName();
            }
        }

        $optionsCity['other'] = 'Other';

        if (null !== $this->getCache()) {
            $this->getCache()->setItem(
                'Litus_CommonBundle_Entity_General_Address_Cities_Streets',
                array(
                    $optionsCity,
                    $optionsStreet,
                )
            );
        }

        return array($optionsCity, $optionsStreet);
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if ('' === $this->get('city')->getValue() && !$this->isRequired()) {
            // empty form
            return array();
        }

        if ($this->get('city')->getValue() !== 'other') {
            unset($specs['other']);

            if (is_array($specs['street'])) {
                foreach ($specs['street'] as $city => $streetSpecification) {
                    if ('type' == $city) {
                        continue;
                    }
                    $specs['street'][$city]['required'] = $this->isRequired() && ($city == 'street_' . $this->get('city')->getValue());
                }
            }
        } else {
            unset($specs['street']);
        }

        return $specs;
    }
}
