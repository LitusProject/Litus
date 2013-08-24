<?php

namespace QuizBundle\Form\Admin\Quiz;

use CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Quiz,
    Zend\Form\Element\Submit;

/**
 * Edits a quiz
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Edit extends Add {

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \QuizBundle\Entity\Quiz $quiz The quiz to populate the form with
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Quiz $quiz, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromQuiz($quiz);
    }
}
