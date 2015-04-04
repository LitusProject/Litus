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

namespace BrBundle\Form\Admin\Order;

use BrBundle\Entity\Company,
    BrBundle\Entity\Product\Order,
    CommonBundle\Entity\General\AcademicYear;

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
     * The maximum number allowed to enter in the corporate order form.
     */
    const MAX_ORDER_NUMBER = 10;

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

        $this->add(array(
            'type'     => 'text',
            'name'     => 'title',
            'label'    => 'Order Title',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'select',
            'name'     => 'company',
            'label'    => 'Company',
            'required' => true,
            'attributes' => array(
                'id'      => 'company',
                'options' => $this->getCompanyArray(),
            ),
        ));

        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        foreach ($companies as $company) {
            $this->add(array(
                'type'     => 'select',
                'name'     => 'contact_' . $company->getId(),
                'label'    => 'Contact',
                'required' => true,
                'attributes' => array(
                    'class'   => 'company_contact',
                    'id'      => 'company_contact_' . $company->getId(),
                    'options' => $this->getContactArray($company),
                ),
                'options'  => array(
                    'input' => array(
                        'required' => false,
                    ),
                ),
            ));
        }

        $this->add(array(
            'type'     => 'text',
            'name'     => 'discount',
            'label'    => 'Discount',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'digits'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'textarea',
            'name'     => 'discount_context',
            'label'    => 'Discount Context',
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'checkbox',
            'name'     => 'tax_free',
            'label'    => 'Tax Free',
        ));

        $products = $this->getProducts();

        foreach ($products as $product) {
            if (!$product->isOld()) {
                $this->add(array(
                    'type'       => 'text',
                    'name'       => 'product_' . $product->getId(),
                    'label'      => $product->getName(),
                    'attributes' => array(
                        'placeholder' => 0,
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'digits',
                                ),
                                array(
                                    'name' => 'between',
                                    'options' => array(
                                        'min' => 0,
                                        'max' => self::MAX_ORDER_NUMBER,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ));
            }
        }

        $this->addSubmit('Add Products', 'product_add');

        if (null !== $this->order) {
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

    private function getProducts()
    {
        return $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product')
            ->findByAcademicYear($this->currentYear);
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs['contact_' . $this->data['company']]['options']['input']['required'] = true;

        return $specs;
    }
}
