<?php

namespace BrBundle\Form\Admin\Contract;

/**
 * Sign a contract.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SignContract extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Invoice\Contract';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'company_reference',
                'label'   => 'Company Reference',
                'options' => array(
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
                'type'  => 'checkbox',
                'name'  => 'tax_free',
                'label' => 'Tax Free',
            )
        );

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'auto_discount_text',
                'label'   => 'Auto Discount Text',
                'value'   => $this->getAutoDiscountText(),
                'options' => array(
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
                'type'    => 'text',
                'name'    => 'discount_text',
                'label'   => 'Discount Text',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Sign Contract', 'contract_edit');
    }

    /**
     * @return string
     */
    private function getAutoDiscountText()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.invoice_auto_discount_text');
    }
}
