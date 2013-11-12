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

namespace FormBundle\Form\Admin\Group;

use Doctrine\ORM\EntityManager,
    FormBundle\Entity\Node\Group,
    Zend\Form\Element\Submit;

/**
 * Edit Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \FormBundle\Entity\Node\Group $group The group we're going to edit
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Group $group, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('start_form');

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'form_edit');
        $this->add($field);

        $this->_populateFromGroup($group);
    }

    private function _populateFromGroup(Group $group)
    {
        $data = array();

        foreach($this->getLanguages() as $language) {
            $data['title_' . $language->getAbbrev()] = $group->getTitle($language, false);
            $data['introduction_' . $language->getAbbrev()] = $group->getIntroduction($language, false);
        }

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('start_form');

        return $inputFilter;
    }
}
