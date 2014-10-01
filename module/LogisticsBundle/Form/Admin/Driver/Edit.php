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

namespace LogisticsBundle\Form\Admin\Driver;

use Doctrine\ORM\EntityManager,
    LogisticsBundle\Entity\Driver,
    Zend\Form\Element\Submit;

/**
 * This form allows the user to edit the driver.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \LogisticsBundle\Form\Admin\Driver\Add
{
    /**
     * @var Driver
     */
    private $_driver;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Driver          $driver
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Driver $driver, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_driver = $driver;

        $this->remove('person_id');
        $this->remove('person_name');

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'driver_edit');
        $this->add($field);

        $this->populateFromDriver($driver);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('person_id');
        $inputFilter->remove('person_name');

        return $inputFilter;
    }
}
