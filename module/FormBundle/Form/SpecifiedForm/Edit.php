<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace FormBundle\Form\SpecifiedForm;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Entry,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Specifield Form Edit
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CommonBundle\Entity\General\Language $language
     * @param \FormBundle\Entity\Node\Form $form
     * @param \FormBundle\Entity\Node\Entry $entry
     * @param \CommonBundle\Entity\Users\Person|null $person
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Language $language, Form $form, Entry $entry, Person $person = null, $name = null)
    {
        parent::__construct($entityManager, $language, $form, $person, $name);

        $field = new Submit('submit');
        $field->setValue($form->getUpdateText($language))
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);

        $this->_populateFromEntry($entry);
    }

    private function _populateFromEntry(Entry $entry)
    {
        $data = array();
        foreach($entry->getFieldEntries() as $fieldEntry) {
            $data['field-' . $fieldEntry->getField()->getId()] = $fieldEntry->getValue();
        }
        $this->setData($data);
    }
}