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

namespace BrBundle\Form\Admin\Company;

use BrBundle\Component\Validator\CompanyName as CompanyNameValidator,
    BrBundle\Entity\Company,
    CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\PhoneNumber as PhonenumberValidator,
    CommonBundle\Form\Admin\Address\Add as AddressForm,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a company.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $years = $this->_getYears();
        $archiveYears = $this->_getArchiveYears();

        $field = new Text('company_name');
        $field->setLabel('Company Name')
            ->setRequired();
        $this->add($field);

        $field = new Select('sector');
        $field->setLabel('Sector')
            ->setRequired()
            ->setAttribute('options', $this->_getSectors());
        $this->add($field);

        $field = new Text('vat_number');
        $field->setLabel('VAT Number')
            ->setRequired();
        $this->add($field);

        $field = new AddressForm('', 'address');
        $field->setLabel('Address');
        $this->add($field);

        $field = new Text('phone_number');
        $field->setLabel('Phone Number')
            ->setAttribute('placeholder', '+CCAAANNNNNN');
        $this->add($field);

        $field = new Text('website');
        $field->setLabel('Website')
            ->setRequired();
        $this->add($field);

        $cvYears = $this->_getArchiveYears();
        foreach ($years as $key => $year) {
            $shortCode = substr($year, 2, 2) . substr($year, 7, 2);
            if (isset($cvYears['archive-' . $shortCode]))
                continue;
            $cvYears['year-' . $key] = $year;
        }
        asort($cvYears);

        $field = new Select('cvbook');
        $field->setLabel('CV Book')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $cvYears);
        $this->add($field);

        $page = new Collection('page_collection');
        $page->setLabel('Page')
            ->setAttribute('id', 'page_form');
        $this->add($page);

        $field = new Select('years');
        $field->setLabel('Page Visible During')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $years);
        $page->add($field);

        $field = new Textarea('summary');
        $field->setLabel('Summary');
        $page->add($field);

        $field = new Textarea('description');
        $field->setLabel('Description');
        $page->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'company_add');
        $this->add($field);
    }

    private function _getYears()
    {
        $years = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $options = array();
        foreach($years as $year)
            $options[$year->getId()] = $year->getCode();

        return $options;
    }

    private function _getArchiveYears()
    {
        $years = unserialize(
            $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_archive_years')
        );

        $options = array();
        foreach($years as $code => $year)
            $options['archive-' . $code] = $year['full_year'] . ' (Archive)';

        return $options;
    }

    private function _getSectors()
    {
        $sectorArray = array();
        foreach (Company::$possibleSectors as $key => $sector)
            $sectorArray[$key] = $sector;

        return $sectorArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputs = $this->get('address')
            ->getInputs();
        foreach($inputs as $input)
            $inputFilter->add($input);

        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'company_name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new CompanyNameValidator($this->_entityManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'website',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'uri',
                        )
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

        if (isset($this->data['page']) && $this->data['page']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'description',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'summary',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'sector',
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
                    'name'     => 'vat_number',
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
