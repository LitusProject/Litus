<?php

namespace BrBundle\Form\Admin\Invoice;

/**
 * Generate a manual invoice.
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class ManualAdd extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Invoice\Manual';

    const FILE_SIZE = '256MB';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'title',
                'label'    => 'Invoice Title',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

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

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'price',
                'label'    => 'Total Price (in cents, Excl. Btw)',
                'required' => true,
                'value'    => 0,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'refund',
                'label' => 'Refund',
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'payment_days',
                'label'    => 'Payment Days',
                'required' => true,
                'value'    => 30,
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
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'File',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The file can be of any type and has a file size limit of ' . self::FILE_SIZE . '.',
                    'size'      => 256,
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FileExtension',
                                'options' => array(
                                    'extension' => 'pdf',
                                ),
                            ),
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Create Manual Invoice', 'invoice_edit');
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
}
