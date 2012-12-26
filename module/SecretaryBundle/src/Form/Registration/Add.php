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

namespace SecretaryBundle\Form\Registration;

use Doctrine\ORM\EntityManager,
    CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Collection,
    CommonBundle\Component\Form\Bootstrap\Element\File,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Validator\PhoneNumber as PhonenumberValidator,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\People\Academic,
    CommonBundle\Form\Address\Add as AddressForm,
    CommonBundle\Form\Address\AddPrimary as PrimaryAddressForm,
    SecretaryBundle\Component\Validator\NoAt as NoAtValidator,
    SecretaryBundle\Entity\Organization\MetaData,
    Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var boolean Are the conditions already checked or not
     */
    protected $_conditionsAlreadyChecked = false;

    /**
     * @param \Zend\Cache\Storage\StorageInterface $cache The cache instance
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $identification The university identification
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(CacheStorage $cache, EntityManager $entityManager, $identification, $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('id', 'register_form');

        $this->_entityManager = $entityManager;

        $this->setAttribute('enctype', 'multipart/form-data');

        $personal = new Collection('personal');
        $personal->setLabel('Personal');
        $this->add($personal);

        $field = new Text('first_name');
        $field->setLabel('First Name')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $personal->add($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $personal->add($field);

        $field = new Text('birthday');
        $field->setLabel('Birthday')
            ->setAttribute('placeholder', 'dd/mm/yyyy')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setRequired();
        $personal->add($field);

        $field = new Select('sex');
        $field->setLabel('Sex')
            ->setAttribute('class', $field->getAttribute('class') . ' input-small')
            ->setAttribute(
                'options',
                array(
                    'm' => 'M',
                    'f' => 'F'
                )
            );
        $personal->add($field);

        $field = new File('profile');
        $field->setLabel('Profile Image');
        $personal->add($field);

        $field = new Text('phone_number');
        $field->setLabel('Phone Number')
            ->setAttribute('placeholder', '+CCAAANNNNNN');
        $personal->add($field);

        $field = new Text('university_identification');
        $field->setLabel('University Identification')
            ->setAttribute('class', $field->getAttribute('class') . ' input-large')
            ->setAttribute('disabled', true)
            ->setValue($identification);
        $personal->add($field);

        $field = new PrimaryAddressForm($cache, $entityManager, 'primary_address', 'primary_address');
        $field->setLabel('Primary Address&mdash;Student Room or Home');
        $this->add($field);

        $field = new AddressForm('secondary_address', 'secondary_address');
        $field->setLabel('Secondary Address&mdash;Home');
        $this->add($field);

        $internet = new Collection('internet');
        $internet->setLabel('Internet');
        $this->add($internet);

        $field = new Text('university_email');
        $field->setLabel('University E-mail')
            ->setAttribute('class', $field->getAttribute('class') . ' input-medium')
            ->setRequired();
        $internet->add($field);

        $field = new Text('personal_email');
        $field->setLabel('Personal E-mail')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $internet->add($field);

        $field = new Checkbox('primary_email');
        $field->setLabel('I want to receive VTK e-mail at my personal e-mail address')
            ->setValue(true);
        $internet->add($field);

        $organization = new Collection('organization');
        $organization->setLabel('Organization')
            ->setAttribute('id', 'organization_info');
        $this->add($organization);

        $field = new Checkbox('become_member');
        $field->setLabel('I want to become a member of the organization (&euro;10)')
            ->setValue(true);
        $organization->add($field);

        $field = new Checkbox('conditions');
        $field->setLabel('I have read and agree with the terms and conditions');
        $organization->add($field);

        $field = new Checkbox('irreeel');
        $field->setLabel('I want to receive my Ir.Reëel at CuDi')
            ->setValue(true);
        $organization->add($field);

        $field = new Checkbox('bakske');
        $field->setLabel('I want to receive \'t Bakske by e-mail')
            ->setValue(false);
        $organization->add($field);

        $field = new Select('tshirt_size');
        $field->setLabel('T-shirt Size')
            ->setAttribute('class', $field->getAttribute('class') . ' input-small')
            ->setAttribute(
                'options',
                MetaData::$possibleSizes
            );
        $organization->add($field);

        $field = new Submit('register');
        $field->setValue('Register')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }

    public function populateFromAcademic(Academic $academic, AcademicYear $academicYear, MetaData $metaData = null)
    {

        $universityEmail = $academic->getUniversityEmail();

        if ($universityEmail) {
            $universityEmail = explode('@', $universityEmail)[0];
        }

        $data = array(
            'first_name' => $academic->getFirstName(),
            'last_name' => $academic->getLastName(),
            'birthday' => $academic->getBirthday() ? $academic->getBirthday()->format('d/m/Y') : '',
            'sex' => $academic->getSex(),
            'phone_number' => $academic->getPhoneNumber(),
            'university_identification' => $academic->getUniversityIdentification(),
            'secondary_address_address_street' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getStreet() : '',
            'secondary_address_address_number' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getNumber() : '',
            'secondary_address_address_mailbox' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getMailbox() : '',
            'secondary_address_address_postal' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getPostal() : '',
            'secondary_address_address_city' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getCity() : '',
            'secondary_address_address_country' => $academic->getSecondaryAddress() ? $academic->getSecondaryAddress()->getCountryCode() : 'BE',
            'university_email' => $universityEmail,
            'personal_email' => $academic->getPersonalEmail(),
            'primary_email' => $academic->getPersonalEmail() == $academic->getEmail(),
            'become_member' => $metaData ? $metaData->becomeMember() : false,
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
                $data['primary_address_address_number'] = $academic->getPrimaryAddress()->getNumber();
                $data['primary_address_address_mailbox'] = $academic->getPrimaryAddress()->getMailbox();
            }
        }

        if ($metaData && $metaData->becomeMember()) {
            $this->get('organization')->get('become_member')
                ->setAttribute('disabled', true);
            $this->get('organization')->get('conditions')
                ->setAttribute('disabled', true);
            $this->_conditionsAlreadyChecked = true;

            $data['conditions'] = true;
            $data['irreeel'] = $metaData->receiveIrReeelAtCudi();
            $data['bakske'] = $metaData->bakskeByMail();
            $data['tshirt_size'] = $metaData->getTshirtSize();
        } else if ($metaData) {
            $data['bakske'] = $metaData->bakskeByMail();
        }

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputs = $this->get('secondary_address')
            ->getInputs();
        foreach($inputs as $input)
            $inputFilter->add($input);

        $inputs =$this->get('primary_address')
                ->getInputs();
        foreach($inputs as $input)
            $inputFilter->add($input);

        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'first_name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'last_name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'birthday',
                    'required' => true,
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
                    'name'     => 'sex',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'profile',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'fileextension',
                            'options' => array(
                                'extension' => 'jpg,png',
                            ),
                        ),
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'extension' => '2MB',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'phone_number',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PhoneNumberValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'university_email',
                    'required' => true,
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
                    'name'     => 'personal_email',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'EmailAddress',
                        ),
                    ),
                )
            )
        );

        if (!$this->_conditionsAlreadyChecked) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'conditions',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'notempty',
                                'options' => array(
                                    'type' => 16,
                                ),
                            ),
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}