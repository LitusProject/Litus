<?php

namespace CudiBundle\Form\Prof\Article;

use SyllabusBundle\Entity\Subject;

/**
 * Add With Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddWithSubject extends \CudiBundle\Form\Prof\Article\Add
{
    /**
     * @var Subject|null
     */
    private $subject;

    public function init()
    {
        parent::init();

        $subjectFieldset = $this->get('subject');
        $subjectField = $subjectFieldset->get('subject');

        $subjectField->get('id')
            ->setAttribute('value', $this->subject->getId());

        $subjectField->get('value')
            ->setAttribute('value', $this->subject->getCode() . ' - ' . $this->subject->getName())
            ->setAttribute('disabled', 'disabled');
    }

    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if (isset($specs['subject'])) {
            unset($specs['subject']);
        }

        return $specs;
    }
}
