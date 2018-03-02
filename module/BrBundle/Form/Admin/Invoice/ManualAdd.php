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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Admin\Invoice;

/**
 * generate a manual invoice.
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class ManualAdd extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Invoice\ManualInvoice';

    const FILESIZE = '256MB';

    public function init()
    {
        parent::init();

        $this->add(array(
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
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'company',
            'label'      => 'Company',
            'required'   => true,
            'attributes' => array(
                'id'      => 'company',
                'options' => $this->getCompanyArray(),
            ),
        ));

        $this->add(array(
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
                        array('name' => 'price'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'refund',
            'label' => 'Refund',
        ));

        $this->add(array(
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
                        array(
                            'name' => 'digits',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'file',
            'name'       => 'file',
            'label'      => 'File',
            'required'   => true,
            'attributes' => array(
                'data-help' => 'The file can be of any type and has a filesize limit of ' . self::FILESIZE . '.',
                'size'      => 256,
            ),
            'options' => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name'    => 'filesize',
                            'options' => array(
                                'max' => self::FILESIZE,
                            ),
                        ),
                        array(
                            'name'    => 'fileextension',
                            'options' => array(
                                'extension' => 'pdf',
                            ),
                        ),
                    ),
                ),
            ),
        ));

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
