<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Admin\Location;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Entity\General\Location,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit Location
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Location\Add
{
    /**
     * @param \CommonBundle\Entity\Users\Role $location The location we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Location $location, $name = null)
    {
        parent::__construct($name);

        $field = new Submit('submit');
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
            'address_postal' => $location->getAddress()->getPostal(),
            'address_city' => $location->getAddress()->getCity(),
            'address_country' => $location->getAddress()->getCountryCode()
        );

        $this->setData($data);
    }
}
