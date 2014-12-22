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

namespace CudiBundle\Form\Prof\Article;


use LogicException,
    SyllabusBundle\Entity\Subject;

/**
 * Add With Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddWithSubject extends Add
{
    /**
     * @var Subject|null
     */
    private $subject;

    public function init()
    {
        if (null === $this->subject) {
            throw new LogicException('Cannot add an article to a null subject');
        }

        parent::init();

        $this->get('subject')
            ->get('subject')
            ->get('id')
            ->setAttribute('value', $this->subject->getId());

        $this->get('subject')
            ->get('subject')
            ->get('value')
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
