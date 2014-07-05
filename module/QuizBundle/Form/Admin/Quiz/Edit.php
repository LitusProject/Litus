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

namespace QuizBundle\Form\Admin\Quiz;

use Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Quiz,
    Zend\Form\Element\Submit;

/**
 * Edits a quiz
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param EntityManager   $entityManager
     * @param Quiz            $quiz          The quiz to populate the form with
     * @param null|string|int $name          Optional name for the form
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
