<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form;

use CommonBundle\Component\Form\Bootstrap\Element\Text,
    FormBundle\Entity\Nodes\Form,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Field
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class SpecifiedForm extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \CudiBundle\Entity\Sales\Article
     */
    protected $_form;

    /**
     * @param \CudiBundle\Entity\Sales\Form $form
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Form $form, $name = null)
    {
        parent::__construct($name);

        $this->_form = $form;

        // Fetch the fields through the repository to have the correct order
        $fields = $entityManager
            ->getRepository('FormBundle\Entity\Field')
            ->findAllByForm($form);

        foreach ($fields as $fieldSpecification) {
            if ('string' == $fieldSpecification->getType()) {
                $field = new Text('field-' . $fieldSpecification->getId());
                $field->setLabel($fieldSpecification->getLabel())
                    ->setRequired($fieldSpecification->isRequired());
                $this->add($field);
            } else {
                // Unable to handle this type
            }
        }

        $field = new Submit('submit');
        $field->setValue($form->getSubmitText())
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            foreach ($this->_form->getFields() as $fieldSpecification) {
                if ('string' == $fieldSpecification->getType()) {
                    $inputFilter->add(
                        $factory->createInput(
                            array(
                                'name'     => 'field-' . $fieldSpecification->getId(),
                                'required' => $fieldSpecification->isRequired(),
                                'filters'  => array(
                                    array('name' => 'StringTrim'),
                                ),
                            )
                        )
                    );
                } else {
                    // Unable to handle this type
                }
            }

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
