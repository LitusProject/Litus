<?php

namespace QuizBundle\Form\Admin\Team;

use CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Team,
    Zend\Form\Element\Submit;

/**
 * Edits a quiz team
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Edit extends Add {

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \QuizBundle\Entity\Team $team The quiz team to populate the form with
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, Team $team, $name = null)
    {
        parent::__construct($entityManager, $team->getQuiz(), $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'quiz_team_edit');
        $this->add($field);

        $this->populateFromTeam($team);
    }
}
