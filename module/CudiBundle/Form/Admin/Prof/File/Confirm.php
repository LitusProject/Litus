<?php

namespace CudiBundle\Form\Admin\Prof\File;

use CudiBundle\Entity\File\ArticleMap;
use LogicException;

/**
 * Confirm File add action
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Confirm extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var ArticleMap|null
     */
    private $mapping;

    public function init()
    {
        if ($this->mapping === null) {
            throw new LogicException('Cannot confirm a null mapping');
        }

        parent::init();

        $this->setAttribute('id', 'uploadFile');

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'description',
                'label'      => 'Description',
                'required'   => true,
                'value'      => $this->mapping->getFile()->getDescription(),
                'attributes' => array(
                    'size' => 70,
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'printable',
                'label'      => 'Printable',
                'attributes' => array(
                    'data-help' => 'Enabling this option will cause the file to be exported by exporting an order. This way these files will be also send to the supplier.',
                ),
            )
        );

        $this->addSubmit('Confirm', 'file_add');
    }

    /**
     * @param  ArticleMap $mapping
     * @return self
     */
    public function setMapping(ArticleMap $mapping)
    {
        $this->mapping = $mapping;

        return $this;
    }
}
