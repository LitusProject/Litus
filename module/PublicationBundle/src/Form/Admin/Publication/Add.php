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

use CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    PublicationBundle\Component\Validator\PublicationTitleValidator,
    PublicationBundle\Entity\Publication,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to add a new Publication
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('title');
        $field->setLabel('Title')
            ->setRequired(true);
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'publication_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {

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
                        'validators' => array(
                            array(
                                'name' => 'regex',
                                'options' => array(
                                    'pattern' => '/^[a-zA-Z0-9 ]*$/',
                                ),
                            ),
                            new PublicationTitleValidator($this->_entityManager)
                        ),
                    )
                )
            );

            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }
}
