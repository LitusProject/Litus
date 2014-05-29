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

namespace BrBundle\Form\Admin\Collaborator;

use BrBundle\Entity\Collaborator,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit an order.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends Add
{

    /**
     * @var \BrBundle\Entity\Product\Collaborator
     */
    private $_collaborator;

    public function __construct(EntityManager $entityManager, Collaborator $collaborator, $options = null)
    {
        parent::__construct($entityManager, $options);

        $this->_collaborator = $collaborator;

        $this->remove('submit');

        $field = new Submit('save');
        $field->setValue('Save')
            ->setAttribute('class', 'collaborator_edit');
        $this->add($field);

        $this->populateFromCollaborator($collaborator);
    }

}
