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

namespace DoorBundle\Form\Admin\Rule;

use Doctrine\ODM\MongoDB\DocumentManager,
    DoorBundle\Document\Rule,
    Zend\Form\Element\Submit;

/**
 * Edit Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var Rule The rule we're going to modify
     */
    private $_rule = null;

    /**
     * @param DocumentManager $documentManager The DocumentManager instance
     * @param Rule            $rule            The rule we're going to modify
     * @param null|string|int $name            Optional name for the element
     */
    public function __construct(DocumentManager $documentManager, Rule $rule, $name = null)
    {
        parent::__construct($documentManager, $name);

        $this->_rule = $rule;

        $this->remove('academic')
            ->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'rule_edit');
        $this->add($field);

        $this->_populateFromRule($rule);
    }

    private function _populateFromRule(Rule $rule)
    {
        $startTime = null;
        if ('' != $rule->getStartTime())
            $startTime = substr($rule->getStartTime(), 0, 2) . ':' . substr($rule->getStartTime(), 2);

        $endTime = null;
        if ('' != $rule->getEndTime())
            $startTime = substr($rule->getEndTime(), 0, 2) . ':' . substr($rule->getEndTime(), 2);

        $data = array(
            'start_date' => $rule->getStartDate()->format('d/m/Y H:i'),
            'end_date' => $rule->getEndDate()->format('d/m/Y H:i'),
            'start_time' => $startTime,
            'end_time' => $endTime,
        );

        $this->setData($data);
    }
}
