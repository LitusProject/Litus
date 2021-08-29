<?php

namespace CudiBundle\Form\Admin\Sale\Booking;

/**
 * Booking by article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Article extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'article',
                'label'      => 'Article',
                'required'   => true,
                'attributes' => array(
                    'id'    => 'article_search',
                    'style' => 'width: 400px;',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadSaleArticle'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'submit',
                'value'      => 'Search',
                'attributes' => array(
                    'class' => 'booking',
                    'id'    => 'search',
                ),
            )
        );
    }
}
