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

use OnBundle\Component\Validator\Name as NameValidator,
    OnBundle\Document\Slug as SlugDocument;

/**
 * Add Slug
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'OnBundle\Hydrator\Slug';

    private $_slug;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'name',
            'label'      => 'Name',
            'required'   => false,
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new NameValidator($this->getDocumentManager(), $this->getSlug()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'url',
            'label'      => 'URL',
            'required'   => true,
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'uri',
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'slug_add');
    }

    /**
     * @param \OnBundle\Document\Slug
     * @return \OnBundle\Form\Admin\Slug\Add
     */
    public function setSlug(SlugDocument $slug)
    {
        $this->_slug = $slug;

        return $this;
    }

    /**
     * @return \OnBundle\Document\Slug
     */
    public function getSlug()
    {
        return $this->_slug;
    }
}
