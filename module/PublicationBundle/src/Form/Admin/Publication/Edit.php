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

namespace PublicationBundle\Form\Admin\Publication;

use PublicationBundle\Entity\Publication,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit,
    PublicationBundle\Component\Validator\PublicationTitleValidator;

/**
 * This form allows the user to edit the Publication.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \PublicationBundle\Form\Admin\Publication\Add
{
    /**
     * @var \PublicationBundle\Entity\Publication
     */
    private $_id;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \PublicationBundle\Entity\Publication $publication
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Publication $publication, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_id = $publication->getId();

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'publication_edit');
        $this->add($field);

        $this->populateFromPublication($publication);
    }

    public function getInputFilter() {
        if ($this->_inputFilter == null) {

            $inputFilter = parent::getInputFilter();
            $factory = new InputFactory();

            $inputFilter->remove('title');

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'title',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new PublicationTitleValidator($this->_entityManager, $this->_id)
                        ),
                    )
                )
            );

            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }
}
