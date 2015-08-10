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

namespace BrBundle\Form\Admin\Contract;

use BrBundle\Entity\Contract;

/**
 * The form used to edit an existing contract
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */

class Edit extends \BrBundle\Form\Admin\Order\GenerateContract
{
    protected $hydrator = 'BrBundle\Hydrator\Contract';

    /**
     * @var Contract
     */
    private $contract;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'text',
            'name'     => 'invoice_nb',
            'label'    => 'Invoice Number',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        foreach ($this->contract->getEntries() as $entry) {
            $this->add(array(
                'type'     => 'textarea',
                'name'     => 'entry_' . $entry->getId(),
                'label'    => $entry->getOrderEntry()->getProduct()->getName(),
                'options'  => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ));
        }

        $this->addSubmit('Save', 'contract_edit');

        if (null !== $this->contract) {
            $this->bind($this->contract);
        }
    }

    /**
     * @param  Contract $contract
     * @return self
     */
    public function setContract(Contract $contract)
    {
        $this->contract = $contract;

        return $this;
    }
}
