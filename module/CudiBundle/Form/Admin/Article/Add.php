<?php

namespace CudiBundle\Form\Admin\Article;

use CudiBundle\Entity\Article;

/**
 * Add Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Article';

    /**
     * @var Article|null
     */
    protected $article;

    public function init()
    {
        parent::init();

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
                        'type'       => 'text',
                        'name'       => 'title',
                        'label'      => 'Title',
                        'required'   => true,
                        'attributes' => array(
                            'size' => 70,
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'authors',
                        'label'      => 'Authors',
                        'required'   => true,
                        'attributes' => array(
                            'size' => 60,
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'publisher',
                        'label'      => 'Publisher',
                        'required'   => true,
                        'attributes' => array(
                            'size' => 40,
                        ),
                        'options'    => array(
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
                        'label'   => 'Year Published',
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
                                        'name'    => 'isbn',
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
                        'type'    => 'text',
                        'name'    => 'name_contact',
                        'label'   => 'Name Contact Person',
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                // TODO validator?
                            ),
                        ),
                    ),
                    array(
                        'type'    => 'text',
                        'name'    => 'email_contact',
                        'label'   => 'E-mail Contact Person',
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                // TODO validator?
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'downloadable',
                        'label'      => 'Downloadable',
                        'attributes' => array(
                            'data-help' => 'Enabling this flag will warn the students this article is also downloadable on the website of the subject.',
                        ),
                    ),
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'same_as_previous_year',
                        'label'      => 'Same As Previous Year',
                        'attributes' => array(
                            'data-help' => 'This flag can be enabled by a docent in \'Prof App\', by this it is possible to show the owners of the store the article is the same as previous year.',
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'type',
                        'label'      => 'Type',
                        'required'   => true,
                        'value'      => 'other',
                        'attributes' => array(
                            'data-help' => 'The type of the article can be:
                        <ul>
                            <li><b>Common:</b> an article which is not mapped to a subject</li>
                            <li><b>Exercises:</b> an article related to exercises</li>
                            <li><b>Notes:</b> notes of the course</li>
                            <li><b>Slides:</b> slides of the course</li>
                            <li><b>Student:</b> an unofficial article of the course (made by students)</li>
                            <li><b>Textbook:</b> a textbook of the course</li>
                            <li><b>Other:</b> any other type</li>
                        </ul>',
                            'options'   => Article::$possibleTypes,
                            'id'        => 'type',
                        ),
                    ),
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'internal',
                        'label'      => 'Internal Article',
                        'attributes' => array(
                            'id'        => 'internal',
                            'data-help' => 'Enabling this flag will show extra options for articles that will be printed by the owners of the store. Articles that are printed by and bought from another supplier doesn\'t need these options.',
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
                        'type'     => 'text',
                        'name'     => 'nb_black_and_white',
                        'label'    => 'Number of B/W Pages',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'Int'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'     => 'text',
                        'name'     => 'nb_colored',
                        'label'    => 'Number of Colored Pages',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'Int'),
                                ),
                            ),
                        ),
                    ),
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
                        'name'  => 'official',
                        'label' => 'Official',
                    ),
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'rectoverso',
                        'label' => 'Recto Verso',
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'front_color',
                        'label'      => 'Front Page Color',
                        'required'   => true,
                        'attributes' => array(
                            'options' => $this->getColors(),
                        ),
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
                    array(
                        'type'  => 'checkbox',
                        'name'  => 'hardcovered',
                        'label' => 'Hardcovered',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'subject_form',
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
                            'id'   => 'subject',
                            'size' => 70,
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
                        'type'       => 'checkbox',
                        'name'       => 'mandatory',
                        'label'      => 'Mandatory',
                        'attributes' => array(
                            'data-help' => 'Enabling this flag will show the students this article is mandatory for the selected subject.',
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'article_add');
    }

    /**
     * @param  Article $article
     * @return self
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;

        return $this;
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

    private function getColors()
    {
        $colors = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\Option\Color')
            ->findAll();

        $colorOptions = array();
        foreach ($colors as $item) {
            $colorOptions[$item->getId()] = $item->getName();
        }

        return $colorOptions;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if (!isset($this->data['article']['internal']) || !$this->data['article']['internal']) {
            if (isset($specs['internal'])) {
                unset($specs['internal']);
            }
        }

        if (!isset($this->data['article']['type']) || $this->data['article']['type'] === 'common' || !isset($this->data['subject_form']['subject']['id'])) {
            if (isset($specs['subject_form'])) {
                unset($specs['subject_form']);
            }
        }

        return $specs;
    }
}
