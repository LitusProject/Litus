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

use BrBundle\Entity\Contract,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to edit an existing contract
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{

    public function __construct(EntityManager $entityManager, Contract $contract, $options = null)
    {
        parent::__construct($options);

        $this->_createFromContract($contract);

        $field = new Submit('Save');
        $field->setValue('Save')
            ->setAttribute('class', 'contract_edit');
        $this->add($field);
    }

    private function _createFromContract(Contract $contract)
    {
        $field = new Text('title');
        $field->setLabel('Title')
            ->setValue($contract->getTitle())
            ->setRequired(true);
        $this->add($field);

        foreach ($contract->getEntries() as $entry)
        {
            $field = new Textarea('entry_' . $entry->getId());
            $field->setLabel($entry->getOrderEntry()->getProduct()->getName())
                ->setValue($entry->getContractText())
                ->setRequired(false);
            $this->add($field);
        }
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'title',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
