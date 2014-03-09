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

namespace OnBundle\Form\Admin\Slug;

use CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ODM\MongoDB\DocumentManager,
    OnBundle\Component\Validator\Name as NameValidator,
    OnBundle\Document\Slug,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Slug
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \OnBundle\Document\Slug The slug we're going to modify
     */
    private $_slug = null;

    /**
     * @param \Doctrine\ODM\MongoDB\DocumentManager $documentManager The DocumentManager instance
     * @param \OnBundle\Document\Slug               $slug            The slug we're going to modify
     * @param null|string|int                       $name            Optional name for the element
     */
    public function __construct(DocumentManager $documentManager, Slug $slug, $name = null)
    {
        parent::__construct($documentManager, $name);

        $this->_slug = $slug;

        $this->get('name')
            ->setRequired();

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'slug_edit');
        $this->add($field);

        $this->_populateFromSlug($slug);
    }

    private function _populateFromSlug(Slug $slug)
    {
        $data = array(
            'name' => $slug->getName(),
            'url' => $slug->getUrl()
        );

        $this->setData($data);
    }
}
