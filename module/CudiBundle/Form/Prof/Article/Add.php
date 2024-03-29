<?php

namespace CudiBundle\Form\Prof\Article;

use CudiBundle\Entity\Article;

/**
 * Add Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Article';

    public function init()
    {
        parent::init();

        $types = Article::$possibleTypes;
        unset($types['common']);

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'article',
                'label'      => 'Article',
                'attributes' => array(
                    'id' => 'article_form',
                ),
                'elements'   => array(
                    array(
                        'type'     => 'text',
                        'name'     => 'title',
                        'label'    => 'Title',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'     => 'text',
                        'name'     => 'authors',
                        'label'    => 'Authors',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'     => 'text',
                        'name'     => 'publisher',
                        'label'    => 'Publisher',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'    => 'text',
                        'name'    => 'year_published',
                        'label'   => 'Publish Year',
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'Int'),
                                    array('name' => 'Year'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'    => 'text',
                        'name'    => 'isbn',
                        'label'   => 'ISBN',
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'Isbn',
                                        'options' => array(
                                            'type' => 'auto',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'    => 'text',
                        'name'    => 'url',
                        'label'   => 'URL',
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'Uri'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'downloadable',
                        'label' => 'Downloadable (on Toledo)',
                    ),
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'same_as_previous_year',
                        'label' => 'Same As Previous Year',
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'type',
                        'label'      => 'Type',
                        'required'   => true,
                        'attributes' => array(
                            'options' => $types,
                        ),
                    ),
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'internal',
                        'label'      => 'Internal Article',
                        'attributes' => array(
                            'id' => 'internal',
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'internal',
                'label'      => 'Internal Article',
                'attributes' => array(
                    'id' => 'internal_form',
                ),
                'elements'   => array(
                    array(
                        'type'       => 'select',
                        'name'       => 'binding',
                        'label'      => 'Binding',
                        'required'   => true,
                        'attributes' => array(
                            'options' => $this->getBindings(),
                        ),
                    ),
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'rectoverso',
                        'label' => 'Recto Verso',
                    ),
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'perforated',
                        'label' => 'Perforated',
                    ),
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'colored',
                        'label' => 'Colored',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'subject',
                'label'      => 'Subject Mapping',
                'attributes' => array(
                    'id' => 'subject_form',
                ),
                'elements'   => array(
                    array(
                        'type'       => 'typeahead',
                        'name'       => 'subject',
                        'label'      => 'Subject',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'subjectSearch',
                        ),
                        'options'    => array(
                            'input' => array(
                                'validators' => array(
                                    array('name' => 'TypeaheadSubject'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'mandatory',
                        'label' => 'Mandatory',
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'btn btn-primary', 'submit')
            ->addSubmit('Save As Dravt', 'btn btn-info', 'draft');
    }

    private function getBindings()
    {
        $bindings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\Option\Binding')
            ->findAll();
        $bindingOptions = array();
        foreach ($bindings as $item) {
            $bindingOptions[$item->getId()] = $item->getName();
        }

        return $bindingOptions;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if (!isset($this->data['article']['internal']) || !$this->data['article']['internal']) {
            if (isset($specs['internal'])) {
                unset($specs['internal']);
            }
        }

        return $specs;
    }
}
