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

namespace OnBundle\Form\Admin\Slug;

use CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ODM\MongoDB\DocumentManager,
    OnBundle\Component\Validator\Name as NameValidator,
    OnBundle\Document\Slug,
    Zend\Form\Element\Submit;

/**
 * Edit Key
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
     * @param \OnBundle\Document\Slug $slug The slug we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(DocumentManager $documentManager, Slug $slug, $name = null)
    {
        parent::__construct($documentManager, $name);

        $this->_slug = $slug;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

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

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->get('name')
            ->setRequired(true)
            ->setValidators(
                new NameValidator($this->_documentManager, $this->_slug)
            );

        return $inputFilter;
    }
}
