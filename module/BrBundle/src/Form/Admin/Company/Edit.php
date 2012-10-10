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

namespace BrBundle\Form\Admin\Company;

use BrBundle\Component\Validator\CompanyName as CompanyNameValidator,
    BrBundle\Entity\Company,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit a company.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \BrBundle\Entity\Company
     */
    private $_company;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \BrBundle\Entity\Company $company
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Company $company, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_company = $company;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'company_edit');
        $this->add($field);

        $this->_populateFromCompany($company);
    }

    private function _populateFromCompany(Company $company)
    {
        $this->setData(
            array(
                'company_name' => $company->getName(),
                'summary' => $company->getSummary(),
                'description' => $company->getDescription(),
                'sector' => $company->getSectorCode(),
                'vat_number' => $company->getVatNumber(),
                'address_street' => $company->getAddress()->getStreet(),
                'address_number' => $company->getAddress()->getNumber(),
                'address_mailbox' => $company->getAddress()->getMailbox(),
                'address_postal' => $company->getAddress()->getPostal(),
                'address_city' => $company->getAddress()->getCity(),
                'address_country' => $company->getAddress()->getCountryCode(),
            )
        );
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = parent::getInputFilter();
            $factory = new InputFactory();

            $inputFilter->remove('company_name');
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'company_name',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new CompanyNameValidator($this->_entityManager, $this->_company),
                        ),
                    )
                )
            );
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
