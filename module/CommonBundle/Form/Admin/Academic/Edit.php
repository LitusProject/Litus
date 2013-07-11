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

namespace CommonBundle\Form\Admin\Academic;

use CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Person\Barcode as BarcodeValidator,
    CommonBundle\Form\Admin\Address\AddPrimary as PrimaryAddressForm,
    CommonBundle\Form\Admin\Address\Add as AddressForm,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    Doctrine\ORM\EntityManager,
    SecretaryBundle\Component\Validator\NoAt as NoAtValidator,
    Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Academic
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Person\Edit
{
    /**
     * @var \CommonBundle\Entity\User\Person The person we're going to modify
     */
    private $_person = null;

    /**
     * @param \Zend\Cache\Storage\StorageInterface $cache The cache instance
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The academic year
     * @param \CommonBundle\Entity\User\Person\Academic $person The person we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(CacheStorage $cache, EntityManager $entityManager, AcademicYear $academicYear, Academic $person, $name = null)
    {
        parent::__construct($entityManager, $person, $name);

        $this->_person = $person;

        $field = new Text('birthday');
        $field->setLabel('Birthday')
            ->setAttribute('placeholder', 'dd/mm/yyyy');
        $this->add($field);

        $field = new PrimaryAddressForm($cache, $entityManager, 'primary_address', 'primary_address');
        $field->setLabel('Primary Address&mdash;Student Room or Home');
        $this->add($field);

        $field = new AddressForm('secondary_address', 'secondary_address');
        $field->setLabel('Secondary Address&mdash;Home');
        $this->add($field);

        $collection = new Collection('organization');
        $collection->setLabel('Organization');
        $this->add($collection);

        $field = new Select('organization_status');
        $field->setLabel('Status')
            ->setAttribute(
                'options',
                array_merge(
                    array(
                        '' => ''
                    ),
                    OrganizationStatus::$possibleStatuses
                )
            );
        $collection->add($field);

        $field = new Text('barcode');
        $field->setLabel('Barcode')
            ->setAttribute('class', 'disableEnter');
        $collection->add($field);

        $collection = new Collection('university');
        $collection->setLabel('University');
        $this->add($collection);

        $field = new Text('university_email');
        $field->setLabel('University E-mail');
        $collection->add($field);

        $field = new Text('university_identification');
        $field->setLabel('Identification');
        $collection->add($field);

        $field = new Select('university_status');
        $field->setLabel('Status')
            ->setRequired()
            ->setAttribute('options', UniversityStatus::$possibleStatuses);
        $collection->add($field);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'academic_edit');
        $this->add($field);

        $this->populateFromAcademic($person, $academicYear);
    }

    public function populateFromAcademic(Academic $academic, AcademicYear $academicYear)
    {
        $universityEmail = $academic->getUniversityEmail();

        if ($universityEmail) {
            $universityEmail = explode('@', $universityEmail)[0];
        }

        $data = array(
            'birthday' => $academic->getBirthday() ? $academic->getBirthday()->format('d/m/Y') : '',
            'organization_status' => $academic->getOrganizationStatus($academicYear) ? $academic->getOrganizationStatus($academicYear)->getStatus() : null,
            'barcode' => $academic->getBarcode() ? $academic->getBarcode()->getBarcode() : '',
            'university_email' => $universityEmail,
            'university_identification' => $academic->getUniversityIdentification(),
            'university_status' => $academic->getUniversityStatus($academicYear) ? $academic->getUniversityStatus($academicYear)->getStatus() : null,
            'secondary_address_address_street' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getStreet() : '',
            'secondary_address_address_number' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getNumber() : '',
            'secondary_address_address_mailbox' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getMailbox() : '',
            'secondary_address_address_postal' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getPostal() : '',
            'secondary_address_address_city' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getCity() : '',
            'secondary_address_address_country' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getCountryCode() : 'BE',
        );

        if ($academic->getPrimaryAddress()) {
            $city = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Address\City')
                ->findOneByName($academic->getPrimaryAddress()->getCity());

            if (null !== $city) {
                $data['primary_address_address_city'] = $city->getId();

                $street = $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Address\Street')
                    ->findOneByCityAndName($city, $academic->getPrimaryAddress()->getStreet());

                $data['primary_address_address_street_' . $city->getId()] = $street ? $street->getId() : 0;
             }
            else {
                $data['primary_address_address_city'] = 'other';
                $data['primary_address_address_postal_other'] = $academic->getPrimaryAddress()->getPostal();
                $data['primary_address_address_city_other'] = $academic->getPrimaryAddress()->getCity();
                $data['primary_address_address_street_other'] = $academic->getPrimaryAddress()->getStreet();
            }
            $data['primary_address_address_number'] = $academic->getPrimaryAddress()->getNumber();
            $data['primary_address_address_mailbox'] = $academic->getPrimaryAddress()->getMailbox();
        }

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        if (null !== $this->get('secondary_address')) {
            $inputs = $this->get('secondary_address')
                ->getInputs();
            foreach($inputs as $input)
                $inputFilter->add($input);
        }

        if (null !== $this->get('primary_address')) {
            $inputs =$this->get('primary_address')
                    ->getInputs();
            foreach($inputs as $input)
                $inputFilter->add($input);
        }

        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'birthday',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'format' => 'd/m/Y',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'barcode',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'barcode',
                            'options' => array(
                                'adapter'     => 'Ean12',
                                'useChecksum' => false,
                            ),
                        ),
                        new BarcodeValidator($this->_entityManager, $this->_person),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'university_email',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new NoAtValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'university_identification',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'alnum'
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'university_status',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
