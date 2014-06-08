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
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Contract   $contract      The contract to edit
     * @param mixed                       $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, Contract $contract, $options = null)
    {
        parent::__construct($entityManager, $options);

        $this->populateFromContract($contract);

        $this->get('company')->setAttribute('disabled', 'disabled');
        $this->get('discount')->setAttribute('disabled', 'disabled');
        $this->remove('sections');

        $field = new Text('invoice_nb');
        $field->setLabel('Invoice number')
            ->setRequired()
            ->setValue($contract->getInvoiceNb());
        $this->add($field);

        foreach ($contract->getEntries() as $entry) {
            $field = new Textarea('entry_' . $entry->getId());
            $field->setLabel($entry->getOrderEntry()->getProduct()->getName())
                ->setValue($entry->getContractText())
                ->setRequired(false);
            $this->add($field);
        }

        $this->remove('submit');
        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'contract_edit');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('company');
        $inputFilter->remove('discount');
        $inputFilter->remove('sections');

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'invoice_nb',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
