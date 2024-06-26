<?php

namespace BrBundle\Form\Admin\Order;

use BrBundle\Entity\Company;
use BrBundle\Entity\Product\Order;
use CommonBundle\Entity\General\AcademicYear;

/**
 * Add a order.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Product\Order';

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var AcademicYear The current academic year
     */
    protected $currentYear;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'company',
                'label'      => 'Company',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'company',
                    'options' => $this->getCompanyArray(),
                ),
            )
        );

        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        foreach ($companies as $company) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'contact_' . $company->getId(),
                    'label'      => 'Contact',
                    'required'   => true,
                    'attributes' => array(
                        'class'   => 'company_contact',
                        'id'      => 'company_contact_' . $company->getId(),
                        'options' => $this->getContactArray($company),
                    ),
                    'options'    => array(
                        'input' => array(
                            'required' => false,
                        ),
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'discount',
                'label'    => 'Discount (in cents)',
                'required' => true,
                'value'    => 0,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'auto_discount',
                'label'      => 'Automatic Discount',
                'value'      => true,
                'attributes' => array(
                    'data-help' => "This checkbox can enable/disable the autodiscount even if an automated discount would be applicable. If no automated discount would be applied this checkbox doesn't matter",
                ),
            )
        );

        $this->addSubmit('Add Products', 'product_add');

        if ($this->order !== null) {
            $this->bind($this->order);
        }
    }

    /**
     * @param  Order $order
     * @return self
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param  AcademicYear $currentYear
     * @return self
     */
    public function setCurrentYear(AcademicYear $currentYear)
    {
        $this->currentYear = $currentYear;

        return $this;
    }

    /**
     * @return array
     */
    private function getCompanyArray()
    {
        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        $companyArray = array(
            '' => '',
        );
        foreach ($companies as $company) {
            $companyArray[$company->getId()] = $company->getName();
        }

        return $companyArray;
    }

    /**
     * @return array
     */
    private function getContactArray(Company $company)
    {
        $contacts = $company->getContacts();

        $contactArray = array(
            '' => '',
        );
        foreach ($contacts as $contact) {
            $contactArray[$contact->getId()] = $contact->getFullName();
        }

        return $contactArray;
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs['contact_' . $this->data['company']]['options']['input']['required'] = true;

        return $specs;
    }
}
