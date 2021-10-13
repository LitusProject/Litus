<?php

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

    /**
     * @var string
     */
    private $lang;

    /**
     * @var string
     */
    private $notLang;

    public function init()
    {
        parent::init();

        foreach ($this->contract->getEntries() as $entry) {
            $this->add(
                array(
                    'type'    => 'textarea',
                    'name'    => 'entry_' . $entry->getId() . '_' . $this->lang,
                    'label'   => $entry->getOrderEntry()->getProduct()->getName(),
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
                    'type'    => 'hidden',
                    'name'    => 'entry_' . $entry->getId() . '_' . $this->notLang,
                    'label'   => $entry->getOrderEntry()->getProduct()->getName(),
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )
            );
        }

        $this->addSubmit('Save', 'contract_edit');

        if ($this->contract !== null) {
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

    /**
     * @param string $lang
     * @return self
     */
    public function setLang(string $lang)
    {
        $this->lang = $lang;
        $this->notLang = $lang == 'nl' ? 'en' : 'nl';

        return $this;
    }
}
