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

namespace SyllabusBundle\Form\Admin\Subject;

use Doctrine\ORM\EntityManager,
    SyllabusBundle\Component\Validator\Subject\Code as CodeValidator,
    SyllabusBundle\Entity\Subject,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var Subject
     */
    private $_subject = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Subject         $subject       The subject we're going to modify
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Subject $subject, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_subject = $subject;

        $this->remove('study_id');
        $this->remove('study');
        $this->remove('mandatory');
        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromSubject($subject);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('study_id');
        $inputFilter->remove('study');

        $inputFilter->remove('code');
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'code',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new CodeValidator($this->_entityManager, $this->_subject),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
