<?php

namespace CudiBundle\Form\Retail\Search;

/**
 * Search Book
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Book extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct($name = null)
    {
        parent::__construct($name, false, false);
    }

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'search_string',
                'label'      => 'Search String',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'search_string',
                    'pattern' => '.{3}.*',
                ),
            )
        );

        $this->remove('csrf');
    }
}
