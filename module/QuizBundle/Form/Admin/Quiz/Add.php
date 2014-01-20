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

namespace QuizBundle\Form\Admin\Quiz;

use CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Quiz,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    CommonBundle\Component\Form\Admin\Element\Select,
    Zend\Form\Element\Submit;

/**
 * Add a new quiz
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);


        $field = new Select('edit_roles');
        $field->setLabel('Edit Roles')
            ->setRequired()
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->_createEditRolesArray());
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'edit_roles',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }

    /**
     * Populates the form with values from the entity
     *
     * @param \QuizBundle\Entity\Quiz $quiz
     */
    public function populateFromQuiz(Quiz $quiz)
    {
        $data = array(
            'name' => $quiz->getName()
        );

        $this->setData($data);
    }


    private function _createEditRolesArray()
    {
        $roles = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findBy(array(), array('name' => 'ASC'));

        $rolesArray = array();
        foreach ($roles as $role) {
            if (!$role->getSystem())
                $rolesArray[$role->getName()] = $role->getName();
        }

        if (empty($rolesArray))
            throw new \RuntimeException('There needs to be at least one role before you can add a quiz');

        return $rolesArray;
    }
}
