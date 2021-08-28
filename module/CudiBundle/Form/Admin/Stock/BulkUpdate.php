<?php

namespace CudiBundle\Form\Admin\Stock;

/**
 * Bulk Update the Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BulkUpdate extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var array
     */
    private $articles = array();

    public function init()
    {
        parent::init();

        foreach ($this->articles as $article) {
            $this->add(
                array(
                    'type'       => 'text',
                    'name'       => 'article_' . $article->getId(),
                    'value'      => $article->getStockValue(),
                    'attributes' => array(
                        'style' => 'width: 70px;',
                    ),
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'Int'),
                            ),
                        ),
                    ),
                )
            );
        }

        $this->addSubmit('Save', 'edit');
    }

    /**
     * @param  array $articles
     * @return self
     */
    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }
}
