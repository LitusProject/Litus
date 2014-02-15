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

namespace CommonBundle\Form\Admin\Location;

use CommonBundle\Entity\General\Location,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Location
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Location\Add
{
    /**
     * @param \CommonBundle\Entity\User\Role $location The location we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Location $location, $name = null)
    {
        parent::__construct($name);

        $this->remove('add');

        $field = new Submit('edit');
        $field->setValue('Save')
            ->setAttribute('class', 'location_edit');
        $this->add($field);

        $this->_populateFromLocation($location);
    }

    private function _populateFromLocation(Location $location)
    {
        $data = array(
            'name' => $location->getName(),
            'address_street' => $location->getAddress()->getStreet(),
            'address_number' => $location->getAddress()->getNumber(),
            'address_mailbox' => $location->getAddress()->getMailbox(),
            'address_postal' => $location->getAddress()->getPostal(),
            'address_city' => $location->getAddress()->getCity(),
            'address_country' => $location->getAddress()->getCountryCode(),
            'latitude' => $location->getLatitude(),
            'longitude' => $location->getLongitude(),
        );

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'latitude',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'longitude',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(),
                )
            )
        );

        return $inputFilter;
    }
}
