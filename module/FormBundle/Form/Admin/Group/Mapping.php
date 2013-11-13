<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace FormBundle\Form\Admin\Group;

use CommonBundle\Component\Form\Admin\Element\Select,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Mapping
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mapping extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Select('form');
        $field->setLabel('Form')
            ->setAttribute('options', $this->getActiveForms());
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'form_add');
        $this->add($field);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    private function getActiveForms()
    {
        $forms = $this->_entityManager
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findAllActive();

        $language = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $options = array();
        foreach($forms as $form) {
            $group = $this->_entityManager
                ->getRepository('FormBundle\Entity\Node\Group\Mapping')
                ->findOneByForm($form);

            if (null == $group)
                $options[$form->getId()] = $form->getTitle($language);
        }

        return $options;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'form',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
