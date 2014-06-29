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

namespace FormBundle\Form\Manage\SpecifiedForm;

use CommonBundle\Component\OldForm\Bootstrap\Element\Collection,
    CommonBundle\Component\OldForm\Bootstrap\Element\Checkbox,
    CommonBundle\Component\OldForm\Bootstrap\Element\Hidden,
    CommonBundle\Component\OldForm\Bootstrap\Element\Text,
    CommonBundle\Entity\General\Language,
    FormBundle\Entity\Node\Form,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Specifield Form Doodle
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doodle extends \FormBundle\Form\SpecifiedForm\Doodle
{
    /**
     * @param \Doctrine\ORM\EntityManager           $entityManager
     * @param \CommonBundle\Entity\General\Language $language
     * @param \FormBundle\Entity\Node\Form          $form
     * @param null|string|int                       $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Language $language, Form $form, $name = null)
    {
        parent::__construct($entityManager, $language, $form, null, null, true, $name);

        $this->remove('first_name');
        $this->remove('last_name');
        $this->remove('email');
        $this->remove('save_as_draft');

        $field = new Checkbox('is_guest');
        $field->setLabel('Is Guest');
        $this->add($field);

        $personForm = new Collection('person_form');
        $personForm->setLabel('Person')
            ->setAttribute('id', 'person_form');
        $this->add($personForm);

        $field = new Hidden('person_id');
        $field->setAttribute('id', 'personId');
        $personForm->add($field);

        $field = new Text('person');
        $field->setLabel('Person')
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $personForm->add($field);

        $guest = new Collection('guest_form');
        $guest->setLabel('Guest')
            ->setAttribute('id', 'guest_form');
        $this->add($guest);

        $field = new Text('first_name');
        $field->setLabel('First Name')
            ->setRequired(true);
        $guest->add($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
            ->setRequired(true);
        $guest->add($field);

        $field = new Text('email');
        $field->setLabel('Email Address')
            ->setRequired(true);
        $guest->add($field);

        $fields = new Collection('fields_form');
        $fields->setLabel('Form');
        $this->add($fields);

        foreach ($this->getElements() as $name => $element) {
            if ($name == 'submit' || $name == 'is_guest')
                continue;

            $this->remove($name);
            $fields->add($element);
        }
    }
}
