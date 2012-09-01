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

namespace BrBundle\Form\Admin\Company;

use BrBundle\Component\Validator\CompanyName as CompanyNameValidator,
    BrBundle\Entity\Company,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
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

        $field = new Text('company_name');
        $field->setLabel('Company Name')
            ->setRequired();
        $this->add($field);

        $field = new Textarea('history');
        $field->setLabel('History')
            ->setRequired();
        $this->add($field);

        $field = new Textarea('description');
        $field->setLabel('Description')
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

        $this->add(new AddressForm('', 'address'));

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'companies_add');
        $this->add($field);
    }

    public function populateFromCompany(Company $company)
    {
        $this->setData(
            array(
                'company_name' => $company->getName(),
                'history' => $company->getHistory(),
                'description' => $company->getDescription(),
                'sector' => $company->getSectorCode(),
                'vat_number' => $company->getVatNumber(),
                'address_street' => $company->getAddress()->getStreet(),
                'address_number' => $company->getAddress()->getNumber(),
                'address_postal' => $company->getAddress()->getPostal(),
                'address_city' => $company->getAddress()->getCity(),
                'address_country' => $company->getAddress()->getCountryCode(),
            )
        );
    }

    private function _getSectors()
    {
        $sectorArray = array();
        foreach (Company::$POSSIBLE_SECTORS as $key => $sector)
            $sectorArray[$key] = $sector;

        return $sectorArray;
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = $this->get('address')->getInputFilter();
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
                        'name'     => 'history',
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
                        'name'     => 'description',
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
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
