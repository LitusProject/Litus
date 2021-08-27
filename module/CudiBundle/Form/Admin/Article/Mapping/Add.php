<?php

namespace CudiBundle\Form\Admin\Article\Mapping;

/**
 * Add Mapping
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'subject',
                'label'      => 'Subject',
                'required'   => true,
                'attributes' => array(
                    'id'   => 'subject',
                    'size' => 70,
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadSubject'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'mandatory',
                'label'      => 'Mandatory',
                'attributes' => array(
                    'data-help' => 'Enabling this flag will show the students this article is mandatory for the selected subject.',
                ),
            )
        );

        $this->addSubmit('Add', 'article_subject_mapping_add');
    }
}
