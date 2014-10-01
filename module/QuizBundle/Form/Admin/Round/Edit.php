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

namespace QuizBundle\Form\Admin\Round;

use CommonBundle\Component\Validator\PositiveNumber as PositiveNumberValidator,
    Doctrine\ORM\EntityManager,
    QuizBundle\Component\Validator\Round\Unique as UniqueRoundValidator,
    QuizBundle\Entity\Round,
    Zend\Form\Element\Submit,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Edits a quiz round
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var Round $round
     */
    private $_round;

    /**
     * @param EntityManager   $entityManager
     * @param Round           $round         The quiz round to populate the form with
     * @param null|string|int $name          Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Round $round, $name = null)
    {
        parent::__construct($entityManager, $round->getQuiz(), $name);

        $this->_round = $round;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromRound($round);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('order');

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'order',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                        new PositiveNumberValidator(),
                        new UniqueRoundValidator($this->_entityManager, $this->_quiz, $this->_round),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
