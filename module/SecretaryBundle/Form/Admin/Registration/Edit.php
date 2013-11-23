<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace SecretaryBundle\Form\Admin\Registration;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Select,
    Doctrine\ORM\EntityManager,
    SecretaryBundle\Entity\Registration,
    SecretaryBundle\Entity\Organization\MetaData,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Registration Data form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \SecretaryBundle\Entity\Registration The registration data
     */
    protected $_registration = null;

    /**
     * @var \SecretaryBundle\Entity\Organization\MetaData The meta data
     */
    protected $_metaData = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \SecretaryBundle\Entity\Registration $registration The registration data
     * @param \SecretaryBundle\Entity\Organization\MetaData $metaData The meta data
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Registration $registration, MetaData $metaData = null, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_registration = $registration;
        $this->_metaData = $metaData;

        $this->remove('person_id');
        $this->remove('person');

        $this->get('payed')->setValue($registration->hasPayed());

        $organization = $registration->getAcademic()->getOrganization($registration->getAcademicYear());
        $this->get('organization')->setValue($organization ? $organization->getId() : 0);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('person_id');
        $inputFilter->remove('person');

        return $inputFilter;
    }
}
