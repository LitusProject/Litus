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
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Form\Admin\Address\Add as AddressForm,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Form\Element\Textarea;

/**
 * Add a company.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $field = new Text('company_name');
        $field->setLabel('Company Name')
            ->setRequired()
            ->addValidator(new CompanyNameValidator($entityManager))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Textarea('history');
        $field->setLabel('History')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Textarea('description');
        $field->setLabel('Description')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Select('sector');
        $field->setLabel('Sector')
            ->setRequired()
            ->setMultiOptions($this->_getSectors())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('vat_number');
        $field->setLabel('VAT Number')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $this->addSubForm(new AddressForm(), 'address');

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'companies_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

    public function populateFromCompany(Company $company)
    {
        $this->populate(
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
}
