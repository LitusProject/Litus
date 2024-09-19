<?php

namespace LogisticsBundle\Form\Admin\Order\OrderArticleMap;

/**
 * Edit OrderArticleMap
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Review extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var array[]
     */
    private $articles = array();

    protected $hydrator = 'LogisticsBundle\Hydrator\Order\OrderArticleMap';

    public function init()
    {
        parent::init();

        foreach ($this->articles as $mapping) {
            $this->add(
                array(
                    'type'       => 'text',
                    'name'       => 'article-amount-' . $mapping->getId(),
                    'label'      => 'Amount',
                    'value'      => $mapping->getAmount(),
                    'attributes' => array(
                        'class' => 'input-very-mini',
                        'id'    => 'article-amount-' . $mapping->getId(),
                    ),
                    'required'   => true,
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

            $this->add(
                array(
                    'type'       => 'textarea',
                    'name'       => 'article-comment-' . $mapping->getId(),
                    'label'      => 'Comment',
                    'value'      => $mapping->getComment(),
                    'attributes' => array(
                        'style' => 'height: 30px;',
                        'id'    => 'article-comment-' . $mapping->getId(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )
            );

            $this->addSubmit('articleReview', 'articleSubmit hide');
        }
    }

    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }
}
