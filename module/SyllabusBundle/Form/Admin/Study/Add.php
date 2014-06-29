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

namespace SyllabusBundle\Form\Admin\Study;

use CommonBundle\Component\OldForm\Admin\Element\Hidden,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Component\Validator\Study\KulId as KulIdValidator,
    SyllabusBundle\Entity\Study,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('kul_id');
        $field->setLabel('KU Leuven Id')
            ->setRequired();
        $this->add($field);

        $field = new Text('title');
        $field->setLabel('Title')
            ->setRequired();
        $this->add($field);

        $field = new Text('phase');
        $field->setLabel('Phase')
            ->setRequired();
        $this->add($field);

        $field = new Text('language');
        $field->setLabel('Language')
            ->setRequired();
        $this->add($field);

        $field = new Hidden('parent_id');
        $field->setAttribute('id', 'parentId');
        $this->add($field);

        $field = new Text('parent');
        $field->setLabel('Parent')
            ->setAttribute('style', 'width: 400px;')
            ->setAttribute('id', 'studySearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    public function populateFromStudy(Study $study)
    {
        $this->setData(
            array(
                'kul_id' => $study->getKulId(),
                'title' => $study->getTitle(),
                'phase' => $study->getPhase(),
                'language' => $study->getLanguage(),
                'parent_id' => $study->getParent() ? $study->getParent()->getId() : '',
                'parent' => $study->getParent() ? $study->getParent()->getFullTitle() : '',
            )
        );
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'kul_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new KulIdValidator($this->_entityManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'title',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'phase',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'language',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'parent_id',
                    'required' => false,
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

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'parent',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
