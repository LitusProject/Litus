<?php

namespace LogisticsBundle\Form\Catalog\Inventory;

/**
 * Search articles
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Search extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->setAttribute('class', 'form-horizontal pull-right col-md-10');

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
    }
}
