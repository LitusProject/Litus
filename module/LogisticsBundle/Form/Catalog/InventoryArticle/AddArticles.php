<?php

namespace LogisticsBundle\Form\Catalog\InventoryArticle;

/**
 * Form used to add articles to an order
 *
 */
class AddArticles extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var array
     */
    private array $articles = array();

    public function init(): void
    {
        parent::init();

        foreach ($this->articles as $article) {
            $this->add(
                array(
                    'type'       => 'text',
                    'name'       => 'article-' . $article->getId(),
                    'attributes' => array(
                        'class'       => 'input-very-mini',
                        'style'       => 'float: left; width: 35%; height: 22px; max-width: 50px; min-width: 35px; margin-left:20%',
                        'id'          => 'article-' . $article->getId(),
                        'placeholder' => '0',
                    ),
                    'options'    => array(
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

        $this->addSubmit('Book', 'btn btn-primary pull-right');
    }

    /**
     * @param  array $articles
     * @return self
     */
    public function setArticles(array $articles): self
    {
        $this->articles = $articles;

        return $this;
    }
}
