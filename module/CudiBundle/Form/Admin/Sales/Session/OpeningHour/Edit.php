<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Sales\Session\OpeningHour;

use CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit Opening Hour
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \CudiBundle\Entity\Sales\Session\OpeningHour $openingHour
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(OpeningHour $openingHour, EntityManager $entityManager, $name = null )
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'clock_edit');
        $this->add($field);

        $this->populateFromOpeningHour($openingHour);
    }
}
