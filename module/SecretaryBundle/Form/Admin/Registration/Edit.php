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
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

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
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_registration = $registration;
        $this->_metaData = $metaData;

        $field = new Checkbox('payed');
        $field->setLabel('Has Payed')
            ->setValue($registration->hasPayed());
        $this->add($field);

        $field = new Checkbox('bakske');
        $field->setLabel('Bakske by E-mail')
            ->setValue($metaData->bakskeByMail());
        $this->add($field);

        $field = new Select('tshirt_size');
        $field->setLabel('T-shirt Size')
            ->setAttribute(
                'options',
                MetaData::$possibleSizes
            )
            ->setValue($metaData->getTshirtSize());
        $this->add($field);

        $organization = $registration->getAcademic()->getOrganization($registration->getAcademicYear());
        $field = new Select('organization');
        $field->setLabel('Organization')
            ->setAttribute('options', $this->_getOrganizations())
            ->setValue($organization ? $organization->getId() : 0);
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'secretary_edit');
        $this->add($field);
    }

    private function _getOrganizations()
    {
        $organizations = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationOptions = array();
        foreach($organizations as $organization)
            $organizationOptions[$organization->getId()] = $organization->getName();

        return $organizationOptions;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();


        return $inputFilter;
    }
}
