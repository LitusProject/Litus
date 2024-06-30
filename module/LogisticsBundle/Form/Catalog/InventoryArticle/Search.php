<?php

namespace LogisticsBundle\Form\Catalog\InventoryArticle;

/**
 * Search articles
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Search extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init(): void
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
