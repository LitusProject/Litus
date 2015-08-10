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

/**
 * generate a contract.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GenerateContract extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Contract';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'text',
            'name'     => 'title',
            'label'    => 'Contract Title',
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
            'type'     => 'text',
            'name'     => 'payment_days',
            'label'    => 'Payment Days',
            'required' => true,
            'value'    => 30,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
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
            'type'     => 'textarea',
            'name'     => 'payement_details',
            'label'    => 'Payement Details',
            'value'    => $this->getPaymentDetailsText(),
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Generate Contract', 'contract_edit');
    }

    /**
     * @return string
     */
    private function getPaymentDetailsText()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.contract_payment_details');
    }
}
