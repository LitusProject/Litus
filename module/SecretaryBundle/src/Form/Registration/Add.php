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

namespace SecretaryBundle\Form\Registration;

use Doctrine\ORM\EntityManager,
    CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Collection,
    CommonBundle\Component\Form\Bootstrap\Element\File,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Form\Address\Add as AddressForm,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Article
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

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
            ->setRequired();
        $personal->add($field);

        $primary_address = new Collection('primary_address');
        $primary_address->setLabel('Primary Address');
        $this->add($primary_address);

        $primary_address->add(new AddressForm('primary_address', 'primary_address_form'));

        $secondary_address = new Collection('secondary_address');
        $secondary_address->setLabel('Secondary Address');
        $this->add($secondary_address);

        $secondary_address->add(new AddressForm('secondary_address', 'secondary_address_form'));

        $internet = new Collection('internet');
        $internet->setLabel('Internet');
        $this->add($internet);

        $field = new Text('university_email');
        $field->setLabel('University Email')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $internet->add($field);

        $field = new Text('personal_email');
        $field->setLabel('Personal Email')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $internet->add($field);

        $field = new Checkbox('primary_email');
        $field->setLabel('I want to receive my email at my personal email')
            ->setValue(true);
        $internet->add($field);

        $organisation = new Collection('organisation');
        $organisation->setLabel('Organisation')
            ->setAttribute('id', 'organisation_info');
        $this->add($organisation);

        $field = new Checkbox('become_member');
        $field->setLabel('I want to become a member of the organisation');
        $organisation->add($field);

        $field = new Checkbox('conditions');
        $field->setLabel('i have read and agree with the terms and conditions');
        $organisation->add($field);

        $field = new Checkbox('irreeel');
        $field->setLabel('I want to receive my Ir. Reëel at CuDi')
            ->setValue(true);
        $organisation->add($field);

        $field = new Checkbox('bakske');
        $field->setLabel('I want to receive \'t Bakske by email')
            ->setValue(false);
        $organisation->add($field);

        $field = new Select('tshirt');
        $field->setLabel('Size of T-shirt')
            ->setAttribute('class', $field->getAttribute('class') . ' input-small')
            ->setAttribute(
                'options',
                array(
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                    'XXL' => 'XXL',
                )
            );
        $organisation->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}