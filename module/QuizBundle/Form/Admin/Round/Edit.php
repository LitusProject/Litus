<?php

namespace QuizBundle\Form\Admin\Round;

use CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Round,
    Zend\Form\Element\Submit;

/**
 * Edits a quiz round
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Edit extends Add {

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \QuizBundle\Entity\Round $round The quiz round to populate the form with
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Round $round, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'quiz_round_edit');
        $this->add($field);

        $this->populateFromRound($round);
    }
}
