<?php

namespace BrBundle\Form\Admin\Order;

use BrBundle\Entity\Product\Order;

/**
 * Generate a contract.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GenerateContract extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Contract';

    /**
     * @var Order
     */
    private $order;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'title',
                'label'    => 'Contract Title',
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

//        $this->add(
//            array(
//                'type'    => 'textarea',
//                'name'    => 'payment_details_nl',
//                'label'   => 'Payment Details NL',
//                'value'   => $this->getPaymentDetailsText('nl'),
//                'options' => array(
//                    'input' => array(
//                        'filters' => array(
//                            array('name' => 'StringTrim'),
//                        ),
//                    ),
//                ),
//            )
//        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'payment_details',
                'label'   => 'Payment Details',
                'value'   => $this->getPaymentDetailsText('nl'),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

//        $this->add(
//            array(
//                'type'    => 'textarea',
//                'name'    => 'payment_details_en',
//                'label'   => 'Payment Details EN',
//                'value'   => $this->getPaymentDetailsText('en'),
//                'options' => array(
//                    'input' => array(
//                        'filters' => array(
//                            array('name' => 'StringTrim'),
//                        ),
//                    ),
//                ),
//            )
//        );

//        $this->add(
//            array(
//                'type'    => 'textarea',
//                'name'    => 'auto_discount_text_nl',
//                'label'   => 'Auto Discount Text NL',
//                'value'   => $this->getAutoDiscountText('nl'),
//                'options' => array(
//                    'input' => array(
//                        'filters' => array(
//                            array('name' => 'StringTrim'),
//                        ),
//                    ),
//                ),
//            )
//        );
//
//        $this->add(
//            array(
//                'type'    => 'textarea',
//                'name'    => 'auto_discount_text_en',
//                'label'   => 'Auto Discount Text EN',
//                'value'   => $this->getAutoDiscountText('en'),
//                'options' => array(
//                    'input' => array(
//                        'filters' => array(
//                            array('name' => 'StringTrim'),
//                        ),
//                    ),
//                ),
//            )
//        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'auto_discount_text',
                'label'   => 'Auto Discount Text',
                'value'   => $this->getAutoDiscountText('nl'),
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
                'type'    => 'textarea',
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

        $this->addSubmit('Generate Contract', 'contract_edit');
    }

    /**
     * @param $lang
     * @return string
     */
    private function getPaymentDetailsText($lang)
    {
        return unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.contract_payment_details')
        )[$lang];
    }

    /**
     * @param $lang
     * @return string
     */
    private function getAutoDiscountText($lang)
    {
        if (isset($this->order) && $this->order->getAutoDiscountPercentage() > 0) {
            return unserialize(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.contract_auto_discount_text')
            )[$lang];
        }

        return '';
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
}
