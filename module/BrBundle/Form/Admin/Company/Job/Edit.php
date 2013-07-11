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

namespace BrBundle\Form\Admin\Company\Job;

use BrBundle\Entity\Company\Job,
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit a job.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \BrBundle\Entity\Company\Job $job
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Job $job, $name = null)
    {
        parent::__construct($name);

        $this->remove('type');
        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'company_edit');
        $this->add($field);

        $this->populateFromJob($job);
    }
}
